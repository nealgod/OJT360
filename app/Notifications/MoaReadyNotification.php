<?php

namespace App\Notifications;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MoaReadyNotification extends Notification
{
    use Queueable;

    protected $company;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Company $company)
    {
        $this->company = $company;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'moa_ready',
            'company_id' => $this->company->id,
            'company_name' => $this->company->name,
            'title' => 'MOA Ready for ' . $this->company->name,
            'message' => 'Your Memorandum of Agreement (MOA) with ' . $this->company->name . ' is ready. Please contact your coordinator to collect the hard copy.',
            'action_url' => route('dashboard'),
        ];
    }
}
