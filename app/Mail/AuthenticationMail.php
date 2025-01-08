<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AuthenticationMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(public array $user)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bem-vindo(a) ao Hedy! Configure sua senha de acesso',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome-new-employee',
            with: [
                'user' => $this->user,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
