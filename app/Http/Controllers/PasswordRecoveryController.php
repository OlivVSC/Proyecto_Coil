<?php

namespace App\Http\Controllers;

use App\Mail\PasswordRecoveryCodeMail;
use App\Models\PasswordRecoveryCode;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class PasswordRecoveryController extends Controller
{
    private const EMAIL_SESSION_KEY = 'password_recovery.email';

    private const VERIFIED_SESSION_KEY = 'password_recovery.verified';

    private const SUCCESS_SESSION_KEY = 'password_recovery.success';

    private const CODE_LIFETIME_MINUTES = 10;

    private const RESEND_WAIT_SECONDS = 60;

    private const MAX_CODE_ATTEMPTS = 5;

    private const MAX_EMAIL_REQUESTS_PER_HOUR = 5;

    private const GENERIC_MESSAGE = 'Por seguridad, no confirmamos si el correo está registrado ni si el mensaje se entregó. Si la cuenta existe y SMTP está configurado, revisa tu bandeja y spam; si no llega, verifica SMTP o espera 60 segundos antes de reenviar.';

    private const SMTP_NOT_CONFIGURED_MESSAGE = 'El envío de correo no está configurado: completa las variables SMTP en .env y ejecuta php artisan config:clear. No se envió ningún código.';

    public function showRequest(): View
    {
        return view('password-recovery', ['stage' => 'request']);
    }

    public function sendCode(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);
        $email = trim($validated['email']);
        $request->session()->put(self::EMAIL_SESSION_KEY, $email);
        $request->session()->forget(self::VERIFIED_SESSION_KEY);

        if (! $this->smtpIsConfigured()) {
            return redirect()->route('password.recovery.verify.form')
                ->with('status', self::SMTP_NOT_CONFIGURED_MESSAGE);
        }

        $emailKey = 'password-recovery:email:'.hash('sha256', Str::lower($email));
        $ipKey = 'password-recovery:ip:'.hash('sha256', $request->ip() ?? 'unknown');
        $limited = RateLimiter::tooManyAttempts($emailKey, self::MAX_EMAIL_REQUESTS_PER_HOUR)
            || RateLimiter::tooManyAttempts($ipKey, 10);

        if (! $limited) {
            RateLimiter::hit($emailKey, 3600);
            RateLimiter::hit($ipKey, 3600);

            $user = User::query()
                ->whereRaw('LOWER(email) = ?', [Str::lower($email)])
                ->first();

            if ($user) {
                try {
                    DB::transaction(function () use ($user): void {
                        $lockedUser = User::query()->whereKey($user->getKey())->lockForUpdate()->first();
                        if (! $lockedUser) {
                            return;
                        }

                        $recovery = PasswordRecoveryCode::query()
                            ->where('user_id', $lockedUser->getKey())
                            ->lockForUpdate()
                            ->first();

                        if ($recovery?->last_sent_at?->isAfter(now()->subSeconds(self::RESEND_WAIT_SECONDS))) {
                            return;
                        }

                        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                        $recovery ??= new PasswordRecoveryCode(['user_id' => $lockedUser->getKey()]);
                        $recovery->fill([
                            'code_hash' => Hash::make($code),
                            'attempts' => 0,
                            'expires_at' => now()->addMinutes(self::CODE_LIFETIME_MINUTES),
                            'last_sent_at' => now(),
                            'verified_at' => null,
                        ]);
                        $recovery->save();

                        Mail::to($lockedUser->email)->send(new PasswordRecoveryCodeMail($code));
                    });
                } catch (TransportExceptionInterface|\InvalidArgumentException $exception) {
                    Log::warning('No se pudo enviar el correo de recuperación.', [
                        'exception_class' => $exception::class,
                    ]);
                }
            }
        }

        return redirect()->route('password.recovery.verify.form')->with('status', self::GENERIC_MESSAGE);
    }

    public function showVerification(Request $request): View|RedirectResponse
    {
        $email = $request->session()->get(self::EMAIL_SESSION_KEY);
        if (! is_string($email) || $email === '') {
            return redirect()->route('password.recovery.request');
        }

        if ($this->verifiedRecovery($request)) {
            return redirect()->route('password.recovery.reset.form');
        }

        return view('password-recovery', [
            'stage' => 'verify',
            'maskedEmail' => $this->maskEmail($email),
        ]);
    }

    public function verifyCode(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'digits' => ['required', 'array', 'size:6'],
            'digits.*' => ['required', 'string', 'regex:/\A[0-9]\z/'],
        ]);
        $email = $request->session()->get(self::EMAIL_SESSION_KEY);
        if (! is_string($email) || $email === '') {
            return redirect()->route('password.recovery.request');
        }

        $code = implode('', $validated['digits']);
        $result = DB::transaction(function () use ($email, $code): array {
            $user = User::query()
                ->whereRaw('LOWER(email) = ?', [Str::lower($email)])
                ->lockForUpdate()
                ->first();
            if (! $user) {
                return ['status' => 'invalid'];
            }

            $recovery = PasswordRecoveryCode::query()
                ->where('user_id', $user->getKey())
                ->lockForUpdate()
                ->first();

            if (! $recovery || $recovery->expires_at->isPast()) {
                return ['status' => 'expired'];
            }
            if ($recovery->attempts >= self::MAX_CODE_ATTEMPTS) {
                return ['status' => 'exhausted'];
            }
            if ($recovery->verified_at) {
                return ['status' => 'used'];
            }
            if (! Hash::check($code, $recovery->code_hash)) {
                $recovery->increment('attempts');

                return ['status' => $recovery->attempts >= self::MAX_CODE_ATTEMPTS ? 'exhausted' : 'invalid'];
            }

            $recovery->forceFill(['verified_at' => now()])->save();

            return [
                'status' => 'verified',
                'user_id' => $user->getKey(),
                'challenge_id' => $recovery->getKey(),
                'expires_at' => $recovery->expires_at->timestamp,
            ];
        });

        if ($result['status'] === 'verified') {
            $request->session()->put(self::VERIFIED_SESSION_KEY, [
                'user_id' => $result['user_id'],
                'challenge_id' => $result['challenge_id'],
                'expires_at' => $result['expires_at'],
            ]);
            $request->session()->regenerate();

            return redirect()->route('password.recovery.reset.form');
        }

        $message = match ($result['status']) {
            'expired' => 'El código venció o no existe. Solicita uno nuevo para continuar.',
            'exhausted' => 'Se agotaron los intentos para este código. Solicita uno nuevo.',
            default => 'El código no es válido. Revísalo e inténtalo de nuevo.',
        };

        return back()->withErrors(['digits' => $message]);
    }

    public function resendCode(Request $request): RedirectResponse
    {
        $email = $request->session()->get(self::EMAIL_SESSION_KEY);
        if (! is_string($email) || $email === '') {
            return redirect()->route('password.recovery.request');
        }

        return $this->sendCode($request->merge(['email' => $email]));
    }

    public function showResetForm(Request $request): View|RedirectResponse
    {
        if (! $this->verifiedRecovery($request)) {
            return redirect()->route('password.recovery.request')
                ->withErrors(['recovery' => 'Verifica un código vigente antes de cambiar la contraseña.']);
        }

        return view('password-recovery', ['stage' => 'reset']);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);
        $authorization = $this->verifiedRecovery($request);
        if (! $authorization) {
            return redirect()->route('password.recovery.request')
                ->withErrors(['recovery' => 'La autorización venció. Solicita un nuevo código.']);
        }

        $updated = DB::transaction(function () use ($authorization, $validated): bool {
            $user = User::query()->whereKey($authorization['user_id'])->lockForUpdate()->first();
            $recovery = PasswordRecoveryCode::query()
                ->whereKey($authorization['challenge_id'])
                ->where('user_id', $authorization['user_id'])
                ->whereNotNull('verified_at')
                ->lockForUpdate()
                ->first();

            if (! $user || ! $recovery || $recovery->expires_at->isPast()) {
                return false;
            }

            $user->password = Hash::make($validated['password']);
            $user->setRememberToken(Str::random(60));
            $user->save();

            $this->revokeDatabaseSessions($user->getKey());
            $recovery->delete();

            return true;
        });

        if (! $updated) {
            $request->session()->forget(self::VERIFIED_SESSION_KEY);

            return redirect()->route('password.recovery.request')
                ->withErrors(['recovery' => 'La autorización venció o ya fue utilizada. Solicita un nuevo código.']);
        }

        $request->session()->forget([self::EMAIL_SESSION_KEY, self::VERIFIED_SESSION_KEY]);
        $request->session()->regenerate();
        $request->session()->flash(self::SUCCESS_SESSION_KEY, true);

        return redirect()->route('password.recovery.success');
    }

    public function success(Request $request): View|RedirectResponse
    {
        if (! $request->session()->pull(self::SUCCESS_SESSION_KEY)) {
            return redirect()->route('login');
        }

        return view('password-recovery', ['stage' => 'success']);
    }

    private function verifiedRecovery(Request $request): ?array
    {
        $authorization = $request->session()->get(self::VERIFIED_SESSION_KEY);
        if (! is_array($authorization)
            || ! isset($authorization['user_id'], $authorization['challenge_id'], $authorization['expires_at'])
            || (int) $authorization['expires_at'] <= now()->timestamp) {
            $request->session()->forget(self::VERIFIED_SESSION_KEY);

            return null;
        }

        $isValid = PasswordRecoveryCode::query()
            ->whereKey($authorization['challenge_id'])
            ->where('user_id', $authorization['user_id'])
            ->whereNotNull('verified_at')
            ->where('expires_at', '>', now())
            ->exists();

        if (! $isValid) {
            $request->session()->forget(self::VERIFIED_SESSION_KEY);

            return null;
        }

        return $authorization;
    }

    private function maskEmail(string $email): string
    {
        [$local, $domain] = array_pad(explode('@', $email, 2), 2, '');
        $visible = mb_substr($local, 0, min(2, mb_strlen($local)));

        return $visible.'***@'.$domain;
    }

    private function smtpIsConfigured(): bool
    {
        $smtp = config('mail.mailers.smtp', []);
        $host = Str::lower(trim((string) ($smtp['host'] ?? '')));
        $username = trim((string) ($smtp['username'] ?? ''));
        $password = trim((string) ($smtp['password'] ?? ''));
        $fromAddress = trim((string) config('mail.from.address'));

        return config('mail.default') === 'smtp'
            && $host !== ''
            && ! in_array($host, ['127.0.0.1', 'localhost', 'smtp.example.com'], true)
            && $username !== ''
            && $password !== ''
            && filter_var($fromAddress, FILTER_VALIDATE_EMAIL) !== false
            && ! Str::endsWith(Str::lower($fromAddress), '@example.com');
    }

    private function revokeDatabaseSessions(int|string $userId): void
    {
        $table = config('session.table', 'sessions');
        if (config('session.driver') === 'database'
            && Schema::hasTable($table)
            && Schema::hasColumn($table, 'user_id')) {
            DB::table($table)->where('user_id', $userId)->delete();
        }
    }
}
