<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CoordinatorInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $inviteLink;

    public function __construct(string $inviteLink)
    {
        $this->inviteLink = $inviteLink;
    }

    public function build(): self
    {
        return $this->subject('Complete your Coordinator Account')
            ->view('emails.coordinator-invitation')
            ->with([
                'link' => $this->inviteLink,
            ]);
    }
}
