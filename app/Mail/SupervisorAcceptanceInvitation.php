<?php

namespace App\Mail;

use App\Models\AcceptanceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SupervisorAcceptanceInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public $acceptanceRequest;

    /**
     * Create a new message instance.
     */
    public function __construct(AcceptanceRequest $acceptanceRequest)
    {
        $this->acceptanceRequest = $acceptanceRequest;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'OJT Acceptance Letter Request - ' . $this->acceptanceRequest->student->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.supervisor-acceptance-invitation',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
