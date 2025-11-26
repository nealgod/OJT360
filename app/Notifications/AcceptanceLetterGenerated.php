<?php

namespace App\Notifications;

use App\Models\AcceptanceLetter;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AcceptanceLetterGenerated extends Notification
{
    use Queueable;

    protected $letter;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(AcceptanceLetter $letter)
    {
        $this->letter = $letter;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail']; // Only send email, not database notification (we use custom notifications table)
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('✅ Your OJT Acceptance Letter Has Been Generated!')
                    ->greeting('Hello '.$notifiable->name.',')
                    ->line('Great news! Your supervisor has successfully generated your OJT Acceptance Letter.')
                    ->line('**Letter Details:**')
                    ->line('• **Company:** '.$this->letter->company->name)
                    ->line('• **Position:** '.$this->letter->job_title)
                    ->line('• **Supervisor:** '.$this->letter->immediate_supervisor)
                    ->line('• **Start Date:** '.$this->letter->start_date->format('F d, Y'))
                    ->line('• **Total Hours:** '.$this->letter->total_hours.' hours')
                    ->action('View Your Documents', route('documents.index'))
                    ->line('The acceptance letter has been automatically added to your documents and is ready for download.')
                    ->line('You can now proceed with your OJT placement!')
                    ->line('Thank you for using OJT360!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        // Different message for coordinator vs student
        $isCoordinator = $notifiable->isCoordinator();

        return [
            'title' => '✅ Acceptance Letter Generated',
            'message' => $isCoordinator
                ? 'A supervisor has generated an acceptance letter for '.$this->letter->student->name.' ('.$this->letter->job_title.' at '.$this->letter->company->name.').'
                : 'Your supervisor has generated your OJT Acceptance Letter for '.$this->letter->job_title.' at '.$this->letter->company->name.'. The letter is now available in your documents.',
            'letter_id' => $this->letter->id,
            'document_id' => $this->letter->document_id,
            'company' => $this->letter->company->name,
            'position' => $this->letter->job_title,
            'student_name' => $this->letter->student->name,
            'start_date' => $this->letter->start_date->format('M d, Y'),
            'action_url' => route('acceptance-letters.download', $this->letter),
            'action_text' => 'View Letter',
        ];
    }
}
