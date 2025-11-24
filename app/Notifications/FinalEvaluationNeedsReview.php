<?php

namespace App\Notifications;

use App\Models\FinalEvaluation;
use Illuminate\Notifications\Notification;

class FinalEvaluationNeedsReview extends Notification
{
    public $evaluation;

    public function __construct(FinalEvaluation $evaluation)
    {
        $this->evaluation = $evaluation;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Final Evaluation Needs Review',
            'message' => 'A final evaluation for ' . $this->evaluation->student_name . ' has been submitted and needs your review.',
            'type' => 'final_evaluation_review',
            'evaluation_id' => $this->evaluation->id,
            'url' => route('coordinator.final-evaluations.show', $this->evaluation),
        ];
    }
    
    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Final Evaluation Needs Review',
            'message' => 'A final evaluation for ' . $this->evaluation->student_name . ' has been submitted and needs your review.',
            'type' => 'final_evaluation_review',
            'evaluation_id' => $this->evaluation->id,
            'url' => route('coordinator.final-evaluations.show', $this->evaluation),
        ];
    }
}
