<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class VerifyWithTemporaryPassword extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $temporaryPassword;

    public function __construct(string $temporaryPassword)
    {
        $this->temporaryPassword = $temporaryPassword;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    protected function verificationUrl(object $notifiable): string
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addHours(24), // 24 hours for coordinators/supervisors
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }

    public function toMail(object $notifiable): MailMessage
    {
        $verifyUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Verify your email and access your OJT360 account')
            ->greeting('Welcome to OJT360, ' . ($notifiable->name ?? ''))
            ->line('An administrator created an account for you. Please verify your email to activate your account.')
            ->action('Verify Email', $verifyUrl)
            ->line('Use these temporary credentials to sign in after verifying:')
            ->line('Email: ' . $notifiable->email)
            ->line('Temporary Password: ' . $this->temporaryPassword)
            ->line('For security, you will be asked to change your password on first login.');
    }
}


