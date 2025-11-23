<?php

namespace App\Notifications;

use App\Models\MonthlyEvaluation;
use Illuminate\Notifications\Notification;

class MonthlyEvaluationNeedsReview extends Notification
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
            'title' => 'New Monthly Evaluation for Review',
            'message' => 'A new monthly evaluation for ' . $this->evaluation->student->name . ' (' . $this->evaluation->getMonthYearLabel() . ') needs your review.',
            'type' => 'evaluation_needs_review',
            'evaluation_id' => $this->evaluation->id,
            'url' => route('coordinator.evaluations.show', $this->evaluation->id),
        ];
    }
}
