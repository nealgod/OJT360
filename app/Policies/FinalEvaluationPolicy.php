<?php

namespace App\Policies;

use App\Models\AcceptanceLetter;
use App\Models\FinalEvaluation;
use App\Models\User;

class FinalEvaluationPolicy
{
    public function create(User $user, User $student): bool
    {
        // Only supervisors can create
        if ($user->role !== 'supervisor') {
            return false;
        }

        // Check if student belongs to this supervisor
        if ($student->studentProfile && (int) $student->studentProfile->supervisor_id !== (int) $user->id) {
            return false;
        }

        // Check if final evaluation already exists for this student
        if (FinalEvaluation::where('student_user_id', $student->id)->exists()) {
            return false;
        }

        // Check if student has acceptance letter (internship must be set up)
        $acceptance = AcceptanceLetter::where('student_user_id', $student->id)->first();
        if (! $acceptance) {
            return false;
        }

        return true;
    }

    public function viewAsSupervisor(User $user, FinalEvaluation $evaluation): bool
    {
        return $user->role === 'supervisor' && (int) $evaluation->supervisor_user_id === (int) $user->id;
    }

    public function viewAsCoordinator(User $user, FinalEvaluation $evaluation): bool
    {
        if ($user->role !== 'coordinator') {
            return false;
        }

        // Check if coordinator is assigned to this evaluation
        if ((int) $evaluation->coordinator_user_id === (int) $user->id) {
            return true;
        }

        // Or check if student belongs to coordinator's program
        $coordinatorProfile = $user->coordinatorProfile;
        if (! $coordinatorProfile) {
            return false;
        }

        $studentProfile = $evaluation->student->studentProfile;
        if (! $studentProfile) {
            return false;
        }

        return $studentProfile->program_id === $coordinatorProfile->program_id;
    }

    public function viewAsStudent(User $user, FinalEvaluation $evaluation): bool
    {
        // Students can only see status notification, not details
        return $user->role === 'intern' && $evaluation->student_user_id === $user->id;
    }

    public function update(User $user, FinalEvaluation $evaluation): bool
    {
        return $user->role === 'supervisor' &&
               (int) $evaluation->supervisor_user_id === (int) $user->id &&
               $evaluation->status === 'draft';
    }

    public function submit(User $user, FinalEvaluation $evaluation): bool
    {
        return $user->role === 'supervisor' &&
               (int) $evaluation->supervisor_user_id === (int) $user->id &&
               $evaluation->canBeSubmitted();
    }

    public function review(User $user, FinalEvaluation $evaluation): bool
    {
        if ($user->role !== 'coordinator') {
            return false;
        }

        // Check if coordinator is assigned to this evaluation
        if ((int) $evaluation->coordinator_user_id === (int) $user->id) {
            return true;
        }

        // Or check if student belongs to coordinator's program
        $coordinatorProfile = $user->coordinatorProfile;
        if (! $coordinatorProfile) {
            return false;
        }

        $studentProfile = $evaluation->student->studentProfile;
        if (! $studentProfile) {
            return false;
        }

        return $studentProfile->program_id === $coordinatorProfile->program_id;
    }
}
