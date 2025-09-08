<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendTemporaryCredentials extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $temporaryPassword;

    public function __construct(User $user, string $temporaryPassword)
    {
        $this->user = $user;
        $this->temporaryPassword = $temporaryPassword;
    }

    public function build()
    {
        return $this->subject('Your OJT360 account details')
            ->markdown('emails.users.temporary-credentials', [
                'user' => $this->user,
                'temporaryPassword' => $this->temporaryPassword,
            ]);
    }
}


