<?php

namespace App\Services;

use App\Models\DocumentRequirement;
use App\Models\StudentDocumentSubmission;
use App\Models\StudentProfile;

class PrePlacementService
{
    /**
     * Recalculate a student's pre-placement completion status based on submitted requirements.
     */
    public static function recalculateForStudent(int $studentId): void
    {
        $requirements = DocumentRequirement::where('type', 'pre_placement')
            ->where('is_required', true)
            ->where('is_active', true)
            ->get();

        if ($requirements->isEmpty()) {
            return;
        }

        // Treat any existing submission for a required pre-placement document as "submitted"
        // Status is no longer used for approval/rejection decisions.
        $submittedRequirementIds = StudentDocumentSubmission::where('student_user_id', $studentId)
            ->whereIn('document_requirement_id', $requirements->pluck('id'))
            ->pluck('document_requirement_id')
            ->unique();

        $allSubmitted = $requirements->every(
            fn ($requirement) => $submittedRequirementIds->contains($requirement->id)
        );

        $profile = StudentProfile::where('user_id', $studentId)->first();

        if (! $profile) {
            return;
        }

        $wasComplete = (bool) $profile->preplacement_complete;

        $profile->update([
            'preplacement_complete' => $allSubmitted,
            'preplacement_completed_at' => $allSubmitted
                ? ($profile->preplacement_completed_at ?? now())
                : null,
            'ojt_status' => $allSubmitted ? 'active' : ($profile->ojt_status ?? 'pending'),
        ]);

        if ($allSubmitted && ! $wasComplete) {
            \App\Models\Notification::create([
                'user_id' => $studentId,
                'type' => 'pre_placement_complete',
                'title' => '✅ Pre-Placement Checklist Complete',
                'message' => 'Great job! All required pre-placement documents are in. Attendance, reports, and other OJT tools are now unlocked.',
                'data' => [
                    'type' => 'pre_placement_complete',
                    'completed_at' => now()->toISOString(),
                ],
            ]);
        }
    }
}
