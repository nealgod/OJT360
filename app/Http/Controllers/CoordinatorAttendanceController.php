<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use Illuminate\Support\Facades\Auth;

class CoordinatorAttendanceController extends Controller
{
    /**
     * Approve a recovered attendance log
     */
    public function approveRecovery(AttendanceLog $log)
    {
        // Verify the log is recovered and pending
        if (! $log->is_recovered || $log->recovery_approved !== null) {
            return response()->json([
                'success' => false,
                'message' => 'This attendance log cannot be approved.',
            ], 400);
        }

        // Approve the recovery
        $log->update([
            'recovery_approved' => true,
            'recovery_approved_at' => now(),
            'recovery_approved_by' => Auth::id(),
            'status' => 'approved',
        ]);

        // Check if student completed required hours after approval
        $user = $log->student;
        if ($user) {
            $this->checkAndUpdateCompletionStatus($user);
        }

        \Log::info('Recovery attendance approved', [
            'log_id' => $log->id,
            'student_id' => $log->student_user_id,
            'approved_by' => Auth::id(),
            'minutes_worked' => $log->minutes_worked,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Recovery approved! Hours have been added to student total.',
        ]);
    }

    /**
     * Reject a recovered attendance log
     */
    public function rejectRecovery(AttendanceLog $log)
    {
        // Verify the log is recovered and pending
        if (! $log->is_recovered || $log->recovery_approved !== null) {
            return response()->json([
                'success' => false,
                'message' => 'This attendance log cannot be rejected.',
            ], 400);
        }

        // Reject the recovery
        $log->update([
            'recovery_approved' => false,
            'recovery_approved_at' => now(),
            'recovery_approved_by' => Auth::id(),
            'status' => 'flagged',
        ]);

        \Log::info('Recovery attendance rejected', [
            'log_id' => $log->id,
            'student_id' => $log->student_user_id,
            'rejected_by' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Recovery rejected. Hours will not be counted.',
        ]);
    }

    /**
     * Check if student has completed required hours and auto-update status
     */
    protected function checkAndUpdateCompletionStatus($user)
    {
        try {
            $studentProfile = $user->studentProfile;

            if (! $studentProfile || $studentProfile->ojt_status !== 'active') {
                return;
            }

            // Calculate total hours completed (exclude pending/rejected recovered logs)
            $totalMinutes = AttendanceLog::where('student_user_id', $user->id)
                ->where(function ($query) {
                    $query->where('is_recovered', false)
                          ->orWhere(function ($q) {
                              $q->where('is_recovered', true)
                                ->where('recovery_approved', true);
                          });
                })
                ->sum('minutes_worked');
            $completedHours = $totalMinutes / 60;

            // Get required hours
            $acceptance = \App\Models\AcceptanceLetter::where('student_user_id', $user->id)
                ->latest()
                ->first();

            $requiredHours = $acceptance?->total_hours
                ?? $studentProfile->required_hours
                ?? $user->getRequiredHours()
                ?? 500;

            $studentProfileUpdates = [
                'completed_hours' => round($completedHours, 2),
            ];

            if (is_null($studentProfile->required_hours)) {
                $studentProfileUpdates['required_hours'] = $requiredHours;
            }

            if ($completedHours >= $requiredHours) {
                $studentProfileUpdates['ojt_status'] = 'completed';
            }

            if (! empty($studentProfileUpdates)) {
                $studentProfile->update($studentProfileUpdates);
            }

            if ($completedHours >= $requiredHours) {
                \Log::info('Student OJT status auto-updated to completed', [
                    'user_id' => $user->id,
                    'completed_hours' => $completedHours,
                    'required_hours' => $requiredHours,
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Error checking completion status: '.$e->getMessage());
        }
    }
}
