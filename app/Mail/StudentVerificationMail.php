<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StudentVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $name;

    public string $studentId;

    public string $link;

    public function __construct(string $name, string $studentId, string $link)
    {
        $this->name = $name;
        $this->studentId = $studentId;
        $this->link = $link;
    }

    public function build(): self
    {
        return $this->subject('Complete your OJT360 registration')
            ->view('emails.student-verification')
            ->with([
                'name' => $this->name,
                'studentId' => $this->studentId,
                'link' => $this->link,
            ]);
    }
}
