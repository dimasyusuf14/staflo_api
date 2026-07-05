<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetPasswordEmail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $defaultPassword;

    public function __construct(User $user, string $defaultPassword)
    {
        $this->user = $user;
        $this->defaultPassword = $defaultPassword;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Password Berhasil Direset',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.reset-password-email',
            with: [
                'user' => $this->user,
                'defaultPassword' => $this->defaultPassword,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
