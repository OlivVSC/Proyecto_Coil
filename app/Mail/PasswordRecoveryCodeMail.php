<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordRecoveryCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $code
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'MarsExplorer | Código de recuperación'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.password-recovery-code',
            with: [
                'code' => $this->code,
            ]
        );
    }
}
