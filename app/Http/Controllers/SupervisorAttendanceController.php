<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use Illuminate\Support\Facades\Auth;

class SupervisorAttendanceController extends Controller
{
    /**
     * Approve a recovered attendance log
     */
    public function approveRecovery(AttendanceLog $log)
    {
        $user = Auth::user();

        // Verify supervisor owns this student
        $student = $log->student;
        if (!$student || !$student->studentProfile || $student->studentProfile->supervisor_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.',
            ], 403);
        }

        // Verify the log is recovered and pending
        if (! $log->is_recovered || $log->recovery_approved !== null) {
            return response()->json([
                'success' => false,
                'message' => 'This attendance log cannot be approved.',
            ], 400);
        }

        // Calculate overtime for recovered attendance
        $overtimeMinutes = 0;
        $acceptance = $student->acceptanceLetter()->latest()->first();
        
        if ($acceptance && isset($acceptance->work_schedule['shift_start']) && isset($acceptance->work_schedule['shift_end'])) {
            try {
                $shiftStart = \Carbon\Carbon::createFromFormat('H:i', $acceptance->work_schedule['shift_start']);
                $shiftEnd = \Carbon\Carbon::createFromFormat('H:i', $acceptance->work_schedule['shift_end']);
                $scheduledBreakMinutes = $acceptance->work_schedule['break_minutes'] ?? config('timezone.default_break_duration', 60);
                $expectedMinutes = $shiftStart->diffInMinutes($shiftEnd) - $scheduledBreakMinutes;
                $overtimeMinutes = max(0, ($log->minutes_worked ?? 0) - $expectedMinutes);
            } catch (\Exception $e) {
                \Log::warning('Overtime calculation error for recovery', [
                    'log_id' => $log->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Approve the recovery
        $log->update([
            'recovery_approved' => true,
            'recovery_approved_at' => now(),
            'recovery_approved_by' => $user->id,
            'overtime_minutes' => $overtimeMinutes,
            'status' => 'approved',
        ]);

        // Check if student completed required hours after approval
        $this->checkAndUpdateCompletionStatus($student);

        \Log::info('Recovery attendance approved by supervisor', [
            'log_id' => $log->id,
            'student_id' => $log->student_user_id,
            'approved_by' => $user->id,
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
        $user = Auth::user();

        // Verify supervisor owns this student
        $student = $log->student;
        if (!$student || !$student->studentProfile || $student->studentProfile->supervisor_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.',
            ], 403);
        }

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
            'recovery_approved_by' => $user->id,
            'status' => 'flagged',
        ]);

        \Log::info('Recovery attendance rejected by supervisor', [
            'log_id' => $log->id,
            'student_id' => $log->student_user_id,
            'rejected_by' => $user->id,
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
