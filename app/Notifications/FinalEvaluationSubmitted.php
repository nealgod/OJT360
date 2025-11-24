<?php

namespace App\Notifications;

use App\Models\FinalEvaluation;
use Illuminate\Notifications\Notification;

class FinalEvaluationSubmitted extends Notification
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
            'title' => 'Final Evaluation Completed',
            'message' => 'Your supervisor has submitted your final OJT performance evaluation.',
            'type' => 'final_evaluation_submitted',
            'evaluation_id' => $this->evaluation->id,
            'url' => route('evaluations.final.status'),
        ];
    }
    
    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Final Evaluation Completed',
            'message' => 'Your supervisor has submitted your final OJT performance evaluation.',
            'type' => 'final_evaluation_submitted',
            'evaluation_id' => $this->evaluation->id,
            'url' => route('evaluations.final.status'),
        ];
    }
}
