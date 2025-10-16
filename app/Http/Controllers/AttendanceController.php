<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\PlacementRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();
        
        // Get today's log specifically for the JavaScript
        $todayLog = AttendanceLog::where('student_user_id', Auth::id())
            ->where('work_date', $today)
            ->first();
            
        // Get all logs for pagination
        $logs = AttendanceLog::where('student_user_id', Auth::id())
            ->orderByDesc('work_date')
            ->paginate(10);
            
        return view('attendance.index', compact('logs', 'todayLog'));
    }

    public function timeIn(Request $request)
    {
        try {
            $request->validate([
                'photo_in' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            ]);

            $user = Auth::user();

            if (!$user->hasActiveOJT()) {
                return back()->with('error', 'You must have an active OJT status to use attendance. Please contact your coordinator.');
            }
            
            $today = now()->toDateString();
            
            // Check if already timed in today
            $existingLog = AttendanceLog::where('student_user_id', $user->id)
                ->where('work_date', $today)
                ->whereNotNull('time_in')
                ->first();

            if ($existingLog) {
                return back()->with('error', 'Already timed in for today.');
            }

            // Get existing log or create new one
            $log = AttendanceLog::where('student_user_id', $user->id)
                ->where('work_date', $today)
                ->first();

            if (!$log) {
                $log = AttendanceLog::create([
                    'student_user_id' => $user->id,
                    'work_date' => $today,
                    'company_id' => $user->studentProfile?->assigned_company_id
                ]);
            }
            

            $path = $request->file('photo_in')->store('attendance-photos', 'public');
            
            // Ensure consistent timezone handling
            $timeIn = now()->setTimezone(config('timezone.default', 'Asia/Manila'));
            
            $log->update([
                'time_in' => $timeIn->format('H:i:s'), // Store in 24-hour format for database
                'photo_in_path' => $path,
                'status' => 'approved',
                'lat_in' => $request->input('lat_in'),
                'lng_in' => $request->input('lng_in'),
            ]);
            
            
            // Check if this is an AJAX request
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Timed in successfully.',
                    'time_in' => $log->time_in
                ]);
            }
            
            return back()->with('success', 'Timed in successfully.');
        } catch (\Exception $e) {
            \Log::error('Time in error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            // Check if this is an AJAX request
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to time in: ' . $e->getMessage()
                ], 400);
            }
            
            return back()->with('error', 'Failed to time in: ' . $e->getMessage());
        }
    }

    public function timeOut(Request $request)
    {
        try {
            $request->validate([
                'photo_out' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            ]);

            $user = Auth::user();
            if (!$user->hasActiveOJT()) {
                return back()->with('error', 'You must have an active OJT status to use attendance. Please contact your coordinator.');
            }

            $today = now()->toDateString();
            $log = AttendanceLog::where('student_user_id', $user->id)
                ->where('work_date', $today)
                ->first();

            if (!$log || !$log->time_in) {
                return back()->with('error', 'Please time in first.');
            }
            if ($log->time_out) {
                return back()->with('error', 'Already timed out.');
            }

            $path = $request->file('photo_out')->store('attendance-photos', 'public');

            // Parse the stored time_in string with proper timezone handling
            try {
                $timeIn = $log->work_date->setTimeFromTimeString($log->time_in)->setTimezone('Asia/Manila');
            } catch (\Exception $e) {
                \Log::error('Time parsing error', [
                    'user_id' => $user->id,
                    'work_date' => $log->work_date,
                    'time_in' => $log->time_in,
                    'error' => $e->getMessage()
                ]);
                return back()->with('error', 'Invalid time in record. Please contact your coordinator.');
            }
            
            // Ensure consistent timezone for time out
            $timeOut = now()->setTimezone(config('timezone.default', 'Asia/Manila'));
            
            // Validate time out is after time in
            if ($timeOut->lt($timeIn)) {
                \Log::warning('Time out before time in', [
                    'user_id' => $user->id,
                    'time_in' => $timeIn->format('H:i:s'),
                    'time_out' => $timeOut->format('H:i:s')
                ]);
                return back()->with('error', 'Time out cannot be before time in. Please check your device clock.');
            }
            
            $totalMinutes = $timeIn->diffInMinutes($timeOut);
            
            // Validate reasonable work duration
            if ($totalMinutes > config('timezone.max_work_duration', 960)) {
                \Log::warning('Excessive work duration', [
                    'user_id' => $user->id,
                    'total_minutes' => $totalMinutes,
                    'hours' => round($totalMinutes / 60, 1)
                ]);
                return back()->with('error', 'Work duration seems excessive. Please contact your coordinator if this is correct.');
            }

            // Load approved placement schedule with validation
            $placement = PlacementRequest::where('student_user_id', $user->id)
                ->where('status', 'approved')
                ->orderByDesc('decided_at')
                ->first();

            if (!$placement) {
                \Log::warning('No approved placement found', ['user_id' => $user->id]);
                return back()->with('error', 'No approved placement found. Please contact your coordinator.');
            }

            $scheduledBreakMinutes = (int)($placement->break_minutes ?? 0);
            
            // Validate break time is reasonable
            if ($scheduledBreakMinutes > config('timezone.max_break_duration', 240)) {
                \Log::warning('Excessive break time', [
                    'user_id' => $user->id,
                    'break_minutes' => $scheduledBreakMinutes
                ]);
                $scheduledBreakMinutes = config('timezone.default_break_duration', 60);
            }

            // Compute productive minutes = total - scheduled break (never below zero)
            $minutes = max(0, $totalMinutes - $scheduledBreakMinutes);

            $log->update([
                'time_out' => $timeOut->format('H:i:s'), // Store in 24-hour format for database
                'photo_out_path' => $path,
                'minutes_worked' => $minutes,
                'lat_out' => $request->input('lat_out'),
                'lng_out' => $request->input('lng_out'),
            ]);
            
            \Log::info('Time out recorded', [
                'user_id' => $user->id,
                'work_date' => $today,
                'time_in' => $timeIn->format('H:i:s'),
                'time_out' => $timeOut->format('H:i:s'),
                'total_minutes' => $totalMinutes,
                'break_minutes' => $scheduledBreakMinutes,
                'minutes_worked' => $minutes,
                'timezone' => 'Asia/Manila'
            ]);

            // Check if this is an AJAX request
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Timed out successfully.',
                    'time_out' => $log->time_out,
                    'minutes_worked' => $minutes
                ]);
            }
            
            return back()->with('success', 'Timed out successfully.');
        } catch (\Exception $e) {
            \Log::error('Time out error: ' . $e->getMessage());
            
            // Check if this is an AJAX request
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to time out: ' . $e->getMessage()
                ], 400);
            }
            
            return back()->with('error', 'Failed to time out: ' . $e->getMessage());
        }
    }

    public function recovery(Request $request)
    {
        try {
            $request->validate([
                'log_id' => 'required|exists:attendance_logs,id',
                'time_out' => 'required|date_format:H:i',
                'reason' => 'required|string|max:500',
                'photo_out' => 'required|image|mimes:jpg,jpeg,png|max:5120'
            ]);

            $user = Auth::user();
            if (!$user->hasActiveOJT()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must have an active OJT status to use attendance.'
                ]);
            }

            // Find the specific incomplete log
            $log = AttendanceLog::where('id', $request->log_id)
                ->where('student_user_id', $user->id)
                ->whereNotNull('time_in')
                ->whereNull('time_out')
                ->first();

            if (!$log) {
                return response()->json([
                    'success' => false,
                    'message' => 'Incomplete attendance record not found or already completed.'
                ]);
            }

            // Store the proof photo
            $photoPath = $request->file('photo_out')->store('attendance-photos', 'public');

            // Parse time_in with proper timezone handling
            try {
                $timeIn = $log->work_date->setTimeFromTimeString($log->time_in)->setTimezone('Asia/Manila');
            } catch (\Exception $e) {
                \Log::error('Recovery time parsing error', [
                    'user_id' => $user->id,
                    'work_date' => $log->work_date,
                    'time_in' => $log->time_in,
                    'error' => $e->getMessage()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid time in record. Please contact your coordinator.'
                ]);
            }
            
            // Parse recovery time out with timezone
            $timeOut = $log->work_date->setTimeFromTimeString($request->time_out)->setTimezone('Asia/Manila');
            
            // Validate time out is after time in
            if ($timeOut->lt($timeIn)) {
                \Log::warning('Recovery time out before time in', [
                    'user_id' => $user->id,
                    'time_in' => $timeIn->format('H:i:s'),
                    'time_out' => $timeOut->format('H:i:s')
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Time out cannot be before time in. Please enter a valid time.'
                ]);
            }
            
            $totalMinutes = $timeIn->diffInMinutes($timeOut);
            
            // Validate reasonable work duration (not more than 16 hours)
            if ($totalMinutes > 960) { // 16 hours
                \Log::warning('Recovery excessive work duration', [
                    'user_id' => $user->id,
                    'total_minutes' => $totalMinutes,
                    'hours' => round($totalMinutes / 60, 1)
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Work duration seems excessive. Please contact your coordinator if this is correct.'
                ]);
            }

            // Load approved placement schedule with validation
            $placement = PlacementRequest::where('student_user_id', $user->id)
                ->where('status', 'approved')
                ->orderByDesc('decided_at')
                ->first();

            if (!$placement) {
                \Log::warning('Recovery no approved placement found', ['user_id' => $user->id]);
                return response()->json([
                    'success' => false,
                    'message' => 'No approved placement found. Please contact your coordinator.'
                ]);
            }

            $scheduledBreakMinutes = (int)($placement->break_minutes ?? 0);
            
            // Validate break time is reasonable (0-4 hours)
            if ($scheduledBreakMinutes > 240) { // 4 hours
                \Log::warning('Recovery excessive break time', [
                    'user_id' => $user->id,
                    'break_minutes' => $scheduledBreakMinutes
                ]);
                $scheduledBreakMinutes = 60; // Default to 1 hour
            }
            
            $minutes = max(0, $totalMinutes - $scheduledBreakMinutes);

            // Update the attendance log
            $log->update([
                'time_out' => $request->time_out,
                'photo_out_path' => $photoPath,
                'minutes_worked' => $minutes,
                'status' => 'approved' // Keep as approved since it's manual recovery
            ]);

            // Log the recovery action for audit purposes
            \Log::info('Attendance recovery completed', [
                'user_id' => $user->id,
                'log_id' => $log->id,
                'work_date' => $log->work_date,
                'time_in' => $timeIn->format('H:i:s'),
                'time_out' => $timeOut->format('H:i:s'),
                'total_minutes' => $totalMinutes,
                'break_minutes' => $scheduledBreakMinutes,
                'minutes_worked' => $minutes,
                'reason' => $request->reason,
                'timezone' => 'Asia/Manila'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Attendance completed successfully! Your hours have been recorded.',
                'minutes_worked' => $minutes,
                'hours_worked' => round($minutes / 60, 1)
            ]);

        } catch (\Exception $e) {
            \Log::error('Recovery attendance error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to complete attendance: ' . $e->getMessage()
            ]);
        }
    }

}


