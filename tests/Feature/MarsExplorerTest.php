<?php

namespace Tests\Feature;

use App\Mail\PasswordRecoveryCodeMail;
use App\Models\PasswordRecoveryCode;
use App\Models\Publicacion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Exception\TransportException;
use Tests\TestCase;

class MarsExplorerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => 'smtp.test.invalid',
            'mail.mailers.smtp.username' => 'test-user',
            'mail.mailers.smtp.password' => 'test-password',
            'mail.from.address' => 'mars-explorer@test.invalid',
        ]);
    }

    private function usuario(string $rol): User
    {
        return User::create([
            'name' => 'Cuenta de prueba',
            'email' => $rol.'@ejemplo.test',
            'password' => 'ClaveSegura2026',
            'rol' => $rol,
        ]);
    }

    private function codigosEnviadosA(string $email): array
    {
        $codigos = [];
        Mail::assertSent(PasswordRecoveryCodeMail::class, function (PasswordRecoveryCodeMail $mensaje) use ($email, &$codigos): bool {
            if ($mensaje->hasTo($email)) {
                $codigos[] = $mensaje->code;

                return true;
            }

            return false;
        });

        $this->assertNotEmpty($codigos);

        return $codigos;
    }

    private function codigoEnviadoA(string $email): string
    {
        return $this->codigosEnviadosA($email)[0];
    }

    public function test_visitante_no_puede_abrir_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect('/');
    }

    public function test_registro_crea_solo_usuarios(): void
    {
        $this->post('/registro', [
            'name' => 'María Prueba', 'email' => 'maria@example.test',
            'password' => 'ClaveSegura2026', 'password_confirmation' => 'ClaveSegura2026',
            'rol' => 'admin', // La petición no puede asignar este rol.
        ])->assertRedirect('/');
        $this->assertDatabaseHas('users', ['email' => 'maria@example.test', 'rol' => 'usuario']);
    }

    public function test_usuario_no_puede_hacer_crud_admin(): void
    {
        $this->actingAs($this->usuario('usuario'))
            ->post('/admin/publicaciones', [
                'tipo' => 'articulo', 'titulo' => 'Prueba',
                'resumen' => 'Descripción', 'contenido' => 'Contenido de prueba',
                'imagen' => 'marte-hielo.jpg',
            ])->assertForbidden();
    }

    public function test_administrador_crea_edita_y_elimina(): void
    {
        $this->actingAs($this->usuario('admin'));
        $this->get('/admin')
            ->assertOk()
            ->assertSee('js/shared.js')
            ->assertSee('js/admin.js');

        $this->post('/admin/publicaciones', [
            'tipo' => 'articulo', 'titulo' => 'Tema nuevo', 'resumen' => 'Nuevo resumen',
            'contenido' => 'Contenido completo.', 'imagen' => 'marte-hielo.jpg',
        ])->assertRedirect('/admin');
        $pub = Publicacion::where('titulo', 'Tema nuevo')->firstOrFail();
        $this->get('/dashboard')->assertOk()->assertSee('Tema nuevo');

        $this->put('/admin/publicaciones/'.$pub->id, [
            'tipo' => 'mision', 'titulo' => 'Título actualizado', 'resumen' => 'Resumen editado',
            'contenido' => 'Contenido editado.', 'imagen' => 'rover.jpg',
        ])->assertRedirect('/admin');
        $this->assertDatabaseHas('publicaciones', ['id' => $pub->id, 'titulo' => 'Título actualizado', 'tipo' => 'mision']);
        $this->get('/dashboard')->assertOk()->assertSee('Título actualizado')->assertDontSee('Tema nuevo');

        $this->delete('/admin/publicaciones/'.$pub->id)->assertRedirect('/admin');
        $this->assertDatabaseMissing('publicaciones', ['id' => $pub->id]);
        $this->get('/dashboard')->assertOk()->assertDontSee('Título actualizado');
    }

    public function test_usuario_puede_entrar_solo_con_su_rol(): void
    {
        $this->usuario('usuario');
        $this->post('/iniciar-sesion', [
            'email' => 'usuario@ejemplo.test',
            'password' => 'ClaveSegura2026',
            'rol' => 'usuario',
        ])->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    public function test_usuario_no_puede_elegir_admin_y_obtener_permisos(): void
    {
        $this->usuario('usuario');
        $this->post('/iniciar-sesion', [
            'email' => 'usuario@ejemplo.test',
            'password' => 'ClaveSegura2026',
            'rol' => 'admin',
        ])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_visitante_no_puede_abrir_la_administracion(): void
    {
        $this->get('/admin')->assertRedirect('/');
    }

    public function test_lector_tampoco_puede_ver_el_panel_de_administracion(): void
    {
        $this->actingAs($this->usuario('usuario'))
            ->get('/admin')->assertForbidden();
    }

    public function test_cerrar_sesion_revoca_el_acceso(): void
    {
        $this->actingAs($this->usuario('usuario'))
            ->post('/salir')->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_solicitud_envia_codigo_hash_y_muestra_correo_oculto(): void
    {
        $user = $this->usuario('usuario');
        Mail::fake();

        $this->post(route('password.recovery.send'), ['email' => $user->email])
            ->assertRedirect(route('password.recovery.verify.form'))
            ->assertSessionHas('status', 'Por seguridad, no confirmamos si el correo está registrado ni si el mensaje se entregó. Si la cuenta existe y SMTP está configurado, revisa tu bandeja y spam; si no llega, verifica SMTP o espera 60 segundos antes de reenviar.');

        $codigo = $this->codigoEnviadoA($user->email);
        $this->assertMatchesRegularExpression('/\A[0-9]{6}\z/', $codigo);

        $recovery = PasswordRecoveryCode::where('user_id', $user->id)->firstOrFail();
        $this->assertNotSame($codigo, $recovery->code_hash);
        $this->assertTrue(Hash::check($codigo, $recovery->code_hash));
        $this->assertSame(0, $recovery->attempts);
        $this->assertNotNull($recovery->expires_at);

        $this->get(route('password.recovery.verify.form'))
            ->assertOk()
            ->assertSee('us***@ejemplo.test')
            ->assertSee('no confirmamos si el correo está registrado')
            ->assertDontSee($codigo);
    }

    public function test_correo_desconocido_recibe_la_misma_confirmacion_generica(): void
    {
        Mail::fake();

        $this->post(route('password.recovery.send'), ['email' => 'desconocido@example.test'])
            ->assertRedirect(route('password.recovery.verify.form'))
            ->assertSessionHas('status', 'Por seguridad, no confirmamos si el correo está registrado ni si el mensaje se entregó. Si la cuenta existe y SMTP está configurado, revisa tu bandeja y spam; si no llega, verifica SMTP o espera 60 segundos antes de reenviar.');

        Mail::assertNothingSent();
    }

    public function test_smtp_sin_configuracion_muestra_error_sin_revelar_si_la_cuenta_existe(): void
    {
        $user = $this->usuario('usuario');
        Mail::fake();
        config([
            'mail.mailers.smtp.host' => '127.0.0.1',
            'mail.mailers.smtp.username' => '',
            'mail.mailers.smtp.password' => '',
            'mail.from.address' => 'hello@example.com',
        ]);

        $known = $this->post(route('password.recovery.send'), ['email' => $user->email]);
        $known->assertRedirect(route('password.recovery.verify.form'))
            ->assertSessionHas('status', 'El envío de correo no está configurado: completa las variables SMTP en .env y ejecuta php artisan config:clear. No se envió ningún código.');

        $unknown = $this->post(route('password.recovery.send'), ['email' => 'desconocido@example.test']);
        $unknown->assertRedirect(route('password.recovery.verify.form'))
            ->assertSessionHas('status', 'El envío de correo no está configurado: completa las variables SMTP en .env y ejecuta php artisan config:clear. No se envió ningún código.');

        $this->get(route('password.recovery.verify.form'))
            ->assertOk()
            ->assertSee('El envío de correo no está configurado')
            ->assertSee('No se envió ningún código');
        Mail::assertNothingSent();
        $this->assertDatabaseMissing('password_recovery_codes', ['user_id' => $user->id]);
    }

    public function test_fallo_smtp_no_confirma_entrega_ni_deja_un_codigo_activo(): void
    {
        $user = $this->usuario('usuario');
        $mailer = \Mockery::mock();
        $mailer->shouldReceive('send')
            ->once()
            ->andThrow(new TransportException('SMTP no disponible'));
        Mail::shouldReceive('to')->once()->with($user->email)->andReturn($mailer);

        $this->post(route('password.recovery.send'), ['email' => $user->email])
            ->assertRedirect(route('password.recovery.verify.form'))
            ->assertSessionHas('status', 'Por seguridad, no confirmamos si el correo está registrado ni si el mensaje se entregó. Si la cuenta existe y SMTP está configurado, revisa tu bandeja y spam; si no llega, verifica SMTP o espera 60 segundos antes de reenviar.');

        $this->assertDatabaseMissing('password_recovery_codes', ['user_id' => $user->id]);
    }

    public function test_solicitud_rechaza_un_correo_invalido(): void
    {
        Mail::fake();

        $this->post(route('password.recovery.send'), ['email' => 'no-es-un-correo'])
            ->assertSessionHasErrors('email');

        Mail::assertNothingSent();
    }

    public function test_codigo_expirado_y_codigo_incorrecto_no_autorizan_el_cambio(): void
    {
        $user = $this->usuario('usuario');
        $recovery = PasswordRecoveryCode::create([
            'user_id' => $user->id,
            'code_hash' => Hash::make('123456'),
            'attempts' => 0,
            'expires_at' => now()->subSecond(),
            'last_sent_at' => now()->subMinute(),
        ]);
        $this->withSession(['password_recovery.email' => $user->email]);

        $this->post(route('password.recovery.verify'), ['digits' => str_split('123456')])
            ->assertSessionHasErrors('digits');
        $this->assertSame(0, $recovery->fresh()->attempts);

        $recovery->update(['expires_at' => now()->addMinutes(10)]);
        $this->post(route('password.recovery.verify'), ['digits' => str_split('654321')])
            ->assertSessionHasErrors('digits');
        $this->assertSame(1, $recovery->fresh()->attempts);
        $this->get(route('password.recovery.reset.form'))
            ->assertRedirect(route('password.recovery.request'));
    }

    public function test_codigo_se_bloquea_despues_de_cinco_intentos_incorrectos(): void
    {
        $user = $this->usuario('usuario');
        $recovery = PasswordRecoveryCode::create([
            'user_id' => $user->id,
            'code_hash' => Hash::make('123456'),
            'attempts' => 0,
            'expires_at' => now()->addMinutes(10),
            'last_sent_at' => now()->subMinute(),
        ]);
        $this->withSession(['password_recovery.email' => $user->email]);

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('password.recovery.verify'), ['digits' => str_split('654321')])
                ->assertSessionHasErrors('digits');
        }

        $this->assertSame(5, $recovery->fresh()->attempts);
        $this->post(route('password.recovery.verify'), ['digits' => str_split('123456')])
            ->assertSessionHasErrors('digits');
        $this->assertSame(5, $recovery->fresh()->attempts);
        $this->get(route('password.recovery.reset.form'))
            ->assertRedirect(route('password.recovery.request'));
    }

    public function test_reenviar_codigo_invalida_el_anterior_y_espera_sesenta_segundos(): void
    {
        $user = $this->usuario('usuario');
        $anterior = '123456';
        $recovery = PasswordRecoveryCode::create([
            'user_id' => $user->id,
            'code_hash' => Hash::make($anterior),
            'attempts' => 0,
            'expires_at' => now()->addMinutes(10),
            'last_sent_at' => now()->subSeconds(61),
        ]);
        Mail::fake();
        $this->withSession(['password_recovery.email' => $user->email]);

        $this->post(route('password.recovery.resend'))
            ->assertRedirect(route('password.recovery.verify.form'));
        $nuevo = $this->codigoEnviadoA($user->email);
        $this->post(route('password.recovery.resend'))
            ->assertRedirect(route('password.recovery.verify.form'));
        $this->assertCount(1, $this->codigosEnviadosA($user->email));

        $recovery->refresh();
        $this->assertFalse(Hash::check($anterior, $recovery->code_hash));

        $recovery->update(['last_sent_at' => now()->subSeconds(61)]);
        $this->post(route('password.recovery.resend'))
            ->assertRedirect(route('password.recovery.verify.form'));
        $codigos = $this->codigosEnviadosA($user->email);
        $this->assertCount(2, $codigos);
        $this->assertNotSame($nuevo, $codigos[1]);

        $this->post(route('password.recovery.verify'), ['digits' => str_split($anterior)])
            ->assertSessionHasErrors('digits');
    }

    public function test_codigo_valido_permite_cambiar_contrasena_sin_alterar_rol(): void
    {
        $user = $this->usuario('admin');
        $rememberToken = $user->getRememberToken();
        Mail::fake();

        $this->post(route('password.recovery.send'), ['email' => $user->email])
            ->assertRedirect(route('password.recovery.verify.form'));
        $codigo = $this->codigoEnviadoA($user->email);
        $this->post(route('password.recovery.verify'), ['digits' => str_split($codigo)])
            ->assertRedirect(route('password.recovery.reset.form'));
        $this->get(route('password.recovery.reset.form'))->assertOk()->assertSee('Crear nueva contraseña');

        $this->post(route('password.recovery.reset'), [
            'password' => 'NuevaClaveSegura2026',
            'password_confirmation' => 'NuevaClaveSegura2026',
        ])->assertRedirect(route('password.recovery.success'));
        $this->get(route('password.recovery.success'))
            ->assertOk()
            ->assertSee('Tu contraseña se ha actualizado correctamente')
            ->assertSee('Volver a iniciar sesión');

        $user->refresh();
        $this->assertSame('admin', $user->rol);
        $this->assertTrue(Hash::check('NuevaClaveSegura2026', $user->password));
        $this->assertNotSame($rememberToken, $user->getRememberToken());
        $this->assertDatabaseMissing('password_recovery_codes', ['user_id' => $user->id]);
        $this->assertGuest();

        $this->post(route('login.post'), [
            'email' => $user->email,
            'password' => 'ClaveSegura2026',
            'rol' => 'admin',
        ])->assertSessionHasErrors('email');
        $this->post(route('login.post'), [
            'email' => $user->email,
            'password' => 'NuevaClaveSegura2026',
            'rol' => 'admin',
        ])->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_campos_de_nueva_contrasena_tienen_controles_de_ojo_accesibles(): void
    {
        $user = $this->usuario('usuario');
        PasswordRecoveryCode::create([
            'user_id' => $user->id,
            'code_hash' => Hash::make('123456'),
            'attempts' => 0,
            'expires_at' => now()->addMinutes(10),
            'last_sent_at' => now()->subMinute(),
            'verified_at' => now(),
        ]);
        $this->withSession([
            'password_recovery.email' => $user->email,
            'password_recovery.verified' => [
                'user_id' => $user->id,
                'challenge_id' => PasswordRecoveryCode::where('user_id', $user->id)->value('id'),
                'expires_at' => now()->addMinutes(10)->timestamp,
            ],
        ]);

        $this->get(route('password.recovery.reset.form'))
            ->assertOk()
            ->assertSee('data-mostrar="clave"', false)
            ->assertSee('data-mostrar="confirmar"', false)
            ->assertSee('aria-label="Mostrar contraseña"', false)
            ->assertSee('aria-pressed="false"', false)
            ->assertSee('<svg viewBox="0 0 24 24"', false)
            ->assertSee('campo.type = visible ? \'text\' : \'password\'', false)
            ->assertDontSee('>Mostrar</button>', false);
    }

    public function test_correo_de_recuperacion_tiene_asunto_y_codigo_en_la_plantilla(): void
    {
        $correo = new PasswordRecoveryCodeMail('012345');

        $this->assertSame(
            'MarsExplorer | Código de recuperación de contraseña',
            $correo->envelope()->subject
        );
        $this->assertStringContainsString('012345', $correo->render());
        $this->assertStringContainsString('Este código expirará en', $correo->render());
    }

    public function test_pantallas_finales_no_se_abren_sin_autorizacion_de_servidor(): void
    {
        $this->get(route('password.recovery.reset.form'))
            ->assertRedirect(route('password.recovery.request'))
            ->assertSessionHasErrors('recovery');
        $this->get(route('password.recovery.success'))
            ->assertRedirect(route('login'));
    }
}
