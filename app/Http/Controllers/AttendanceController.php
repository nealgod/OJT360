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
        $logs = AttendanceLog::where('student_user_id', Auth::id())
            ->orderByDesc('work_date')
            ->paginate(10);
        return view('attendance.index', compact('logs'));
    }

    public function timeIn(Request $request)
    {
        try {
            \Log::info('Time in request received', [
                'user_id' => Auth::id(),
                'has_file' => $request->hasFile('photo_in'),
                'file_size' => $request->hasFile('photo_in') ? $request->file('photo_in')->getSize() : 'no file',
                'all_input' => $request->all()
            ]);

            $request->validate([
                'photo_in' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            ]);

            $user = Auth::user();
            \Log::info('User OJT status', [
                'user_id' => $user->id,
                'has_active_ojt' => $user->hasActiveOJT(),
                'ojt_status' => $user->studentProfile?->ojt_status
            ]);

            if (!$user->hasActiveOJT()) {
                \Log::warning('User attempted time in without active OJT', [
                    'user_id' => $user->id,
                    'has_student_profile' => $user->studentProfile ? 'yes' : 'no',
                    'ojt_status' => $user->studentProfile?->ojt_status ?? 'no profile'
                ]);
                return back()->with('error', 'You must have an active OJT status to use attendance. Please contact your coordinator.');
            }

            $today = now()->toDateString();
            $log = AttendanceLog::firstOrCreate(
                ['student_user_id' => $user->id, 'work_date' => $today],
                ['company_id' => $user->studentProfile?->assigned_company_id]
            );

            if ($log->time_in) {
                return back()->with('error', 'Already timed in.');
            }

            $path = $request->file('photo_in')->store('attendance-photos', 'public');
            $log->update([
                'time_in' => now()->setTimezone('Asia/Manila')->format('H:i:s'), // Store in 24-hour format for database
                'photo_in_path' => $path,
                'status' => 'approved',
                'lat_in' => $request->input('lat_in'),
                'lng_in' => $request->input('lng_in'),
            ]);

            \Log::info('Time in successful', ['log_id' => $log->id]);
            
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

            // Parse the stored time_in string and convert to today's date
            try {
                $timeIn = $log->work_date->setTimeFromTimeString($log->time_in)->setTimezone('Asia/Manila');
            } catch (\Exception $e) {
                // Fallback for old format or invalid time
                $timeIn = $log->work_date->setTime(8, 0, 0)->setTimezone('Asia/Manila'); // Default to 8 AM if parsing fails
            }
            
            $timeOut = now()->setTimezone('Asia/Manila');
            $totalMinutes = max(0, $timeIn->diffInMinutes($timeOut));

            // Load approved placement schedule (if available)
            $placement = PlacementRequest::where('student_user_id', $user->id)
                ->where('status', 'approved')
                ->orderByDesc('decided_at')
                ->first();

            $scheduledBreakMinutes = (int)($placement->break_minutes ?? 0);

            // Compute productive minutes = total - scheduled break (never below zero)
            $minutes = max(0, $totalMinutes - $scheduledBreakMinutes);

            $log->update([
                'time_out' => $timeOut->format('H:i:s'), // Store in 24-hour format for database
                'photo_out_path' => $path,
                'minutes_worked' => $minutes,
                'lat_out' => $request->input('lat_out'),
                'lng_out' => $request->input('lng_out'),
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
}


