<?php

namespace App\Policies;

use App\Models\MonthlyEvaluation;
use App\Models\User;

class MonthlyEvaluationPolicy
{
    /**
     * Determine if the supervisor can view the evaluation.
     */
    public function viewAsSupervisor(User $user, MonthlyEvaluation $evaluation): bool
    {
        return $user->isSupervisor() && $evaluation->supervisor_user_id === $user->id;
    }

    /**
     * Determine if the supervisor can update the evaluation.
     */
    public function update(User $user, MonthlyEvaluation $evaluation): bool
    {
        return $user->isSupervisor()
            && $evaluation->supervisor_user_id === $user->id
            && $evaluation->status === 'draft';
    }

    /**
     * Determine if the supervisor can submit the evaluation.
     */
    public function submit(User $user, MonthlyEvaluation $evaluation): bool
    {
        return $user->isSupervisor()
            && $evaluation->supervisor_user_id === $user->id
            && $evaluation->status === 'draft';
    }

    /**
     * Determine if the coordinator can view the evaluation.
     */
    public function viewAsCoordinator(User $user, MonthlyEvaluation $evaluation): bool
    {
        return $user->isCoordinator() && $evaluation->coordinator_user_id === $user->id;
    }

    /**
     * Determine if the coordinator can review the evaluation.
     */
    public function review(User $user, MonthlyEvaluation $evaluation): bool
    {
        return $user->isCoordinator() && $evaluation->coordinator_user_id === $user->id;
    }
}
