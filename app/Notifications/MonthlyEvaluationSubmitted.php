<?php

namespace App\Notifications;

use App\Models\MonthlyEvaluation;
use Illuminate\Notifications\Notification;

class MonthlyEvaluationSubmitted extends Notification
{
    public $evaluation;

    public function __construct(MonthlyEvaluation $evaluation)
    {
        $this->evaluation = $evaluation;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Monthly Evaluation Submitted',
            'message' => 'Your supervisor has submitted your monthly progress evaluation for ' . $this->evaluation->getMonthYearLabel() . '.',
            'type' => 'evaluation_submitted',
            'evaluation_id' => $this->evaluation->id,
            'url' => route('evaluations.index'),
        ];
    }
}
