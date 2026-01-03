<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
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

            if ($user->studentProfile?->ojt_status === 'completed') {
                return back()->with('error', 'You have completed your OJT. Attendance logging is disabled.');
            }

            if (! $user->hasActiveOJT()) {
                return back()->with('error', 'You must have an active OJT status to use attendance.');
            }

            // Check Start Date
            $acceptance = \App\Models\AcceptanceLetter::where('student_user_id', $user->id)
                ->latest('start_date')
                ->first();

            if ($acceptance && $acceptance->start_date) {
                if (now()->startOfDay()->lt($acceptance->start_date->startOfDay())) {
                    return back()->with('error', 'Your OJT schedule begins on '.$acceptance->start_date->format('F j, Y').'.');
                }
            }

            $today = now()->toDateString();
            
            // Get existing log
            $log = AttendanceLog::where('student_user_id', $user->id)
                ->where('work_date', $today)
                ->first();

            $path = $request->file('photo_in')->store('attendance-photos', 'public');
            $currentTime = now()->setTimezone(config('timezone.default', 'Asia/Manila'))->format('H:i:s');

            // --- Logic for AM IN vs PM IN ---
            if (! $log) {
                // Case 1: AM IN (First punch of the day)
                $log = AttendanceLog::create([
                    'student_user_id' => $user->id,
                    'work_date' => $today,
                    'company_id' => $user->studentProfile?->assigned_company_id,
                    'am_in_time' => $currentTime,
                    'am_in_photo' => $path,
                    'am_in_lat' => $request->input('lat_in'),
                    'am_in_lng' => $request->input('lng_in'),
                    'status' => 'approved', // In Progress (morning)
                ]);
                $msg = 'Timed In (Morning) successfully.';
            } elseif ($log->am_out_time && ! $log->pm_in_time) {
                // Case 2: PM IN (After lunch)
                // When student returns after morning shift, status should be pending for verification
                $log->update([
                    'pm_in_time' => $currentTime,
                    'pm_in_photo' => $path,
                    'pm_in_lat' => $request->input('lat_in'),
                    'pm_in_lng' => $request->input('lng_in'),
                    'status' => 'approved', // Keep as approved (will remain approved at PM OUT)
                ]);
                $msg = 'Timed In (Afternoon) successfully.';
            } else {
                 if ($log->am_in_time && !$log->am_out_time) {
                     return back()->with('error', 'You are currently timed in for Morning. Please Time Out first.');
                 }
                return back()->with('error', 'Invalid punch sequence. Please check your status.');
            }

            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => $msg]);
            }
            return back()->with('success', $msg);

        } catch (\Exception $e) {
            \Log::error('Time in error: '.$e->getMessage());
            if (request()->ajax()) return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
            return back()->with('error', 'Error: '.$e->getMessage());
        }
    }

    public function timeOut(Request $request)
    {
        try {
            $request->validate([
                'photo_out' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            ]);

            $user = Auth::user();
            if (! $user->hasActiveOJT()) {
                return back()->with('error', 'You must have an active OJT status to use attendance. Please contact your coordinator.');
            }

            $acceptance = \App\Models\AcceptanceLetter::where('student_user_id', $user->id)
                ->latest('start_date')
                ->first();

            if ($acceptance && $acceptance->start_date) {
                $startDate = $acceptance->start_date->startOfDay();
                if (now()->startOfDay()->lt($startDate)) {
                    $message = 'Your OJT schedule begins on '.$acceptance->start_date->format('F j, Y').'. Time out will be available on that date.';
                    if ($request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => $message,
                        ], 422);
                    }

                    return back()->with('error', $message);
                }
            }

            $today = now()->toDateString();
            $log = AttendanceLog::where('student_user_id', $user->id)
                ->where('work_date', $today)
                ->first();

            if (! $log) {
                return back()->with('error', 'No attendance record found for today.');
            }

            $path = $request->file('photo_out')->store('attendance-photos', 'public');
            $currentTime = now()->setTimezone(config('timezone.default', 'Asia/Manila'))->format('H:i:s');

            // --- Logic for AM OUT vs PM OUT ---
            if ($log->am_in_time && ! $log->am_out_time) {
                // Case 1: AM OUT (Lunch break / Half Day)
                
                // Calculate AM Duration
                $amStart = \Carbon\Carbon::parse($log->am_in_time);
                $amEnd = \Carbon\Carbon::parse($currentTime);
                $amMinutes = max(0, $amStart->diffInMinutes($amEnd));
                
                // Update log with AM Out time AND calculated minutes
                $log->update([
                    'am_out_time' => $currentTime,
                    'am_out_photo' => $path,
                    'am_out_lat' => $request->input('lat_out'),
                    'am_out_lng' => $request->input('lng_out'),
                    'minutes_worked' => $amMinutes, // Bank these minutes immediately
                ]);
                
                $msg = 'Timed Out (Morning) successfully.';

            } elseif ($log->pm_in_time && ! $log->pm_out_time) {
                // Case 2: PM OUT (End of day)
                
                // Recalculate AM Duration
                $amStart = \Carbon\Carbon::parse($log->am_in_time);
                $amEnd = \Carbon\Carbon::parse($log->am_out_time);
                $amMinutes = max(0, $amStart->diffInMinutes($amEnd));

                // Calculate PM Duration
                $pmStart = \Carbon\Carbon::parse($log->pm_in_time);
                $pmEnd = \Carbon\Carbon::parse($currentTime);
                $pmMinutes = max(0, $pmStart->diffInMinutes($pmEnd));

                $totalMinutes = $amMinutes + $pmMinutes;

                $log->update([
                    'pm_out_time' => $currentTime,
                    'pm_out_photo' => $path,
                    'pm_out_lat' => $request->input('lat_out'),
                    'pm_out_lng' => $request->input('lng_out'),
                    'minutes_worked' => $totalMinutes,
                    'overtime_minutes' => max(0, $totalMinutes - 480), 
                    'status' => 'approved' 
                ]);
                
                $this->checkAndUpdateCompletionStatus($user); // Auto-complete check

                $msg = 'Timed Out (Afternoon) successfully. Day Complete.';
            } else {
                return back()->with('error', 'Invalid punch sequence. check status.');
            }

            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => $msg]);
            }
            return back()->with('success', $msg);

        } catch (\Exception $e) {
            \Log::error('Time out error: '.$e->getMessage());
            if (request()->ajax()) return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
            return back()->with('error', 'Error: '.$e->getMessage());
        }
    }

    public function recovery(Request $request)
    {
        try {
            $request->validate([
                'log_id' => 'required|exists:attendance_logs,id',
                'time_out' => 'required|date_format:H:i',
                'reason' => 'required|string|max:500',
                'photo_out' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
                // New validation for optional Whole Day Recovery
                'whole_day' => 'nullable|in:on,true,1',
                'pm_in' => 'nullable|required_if:whole_day,on|date_format:H:i|after:time_out',
                'pm_out' => 'nullable|required_if:whole_day,on|date_format:H:i|after:pm_in',
            ]);

            if (!$request->hasFile('photo_out')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please upload a photo proof to complete your recovery request.',
                ]);
            }

            $user = Auth::user();
            
            // Check if student has completed their OJT
            if ($user->studentProfile?->ojt_status === 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'You have completed your OJT. Attendance recovery is disabled.',
                ]);
            }
            
            if (! $user->hasActiveOJT()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must have an active OJT status to use attendance.',
                ]);
            }

            // Find the specific incomplete log
            $log = AttendanceLog::where('id', $request->log_id)
                ->where('student_user_id', $user->id)
                ->where(function($q) {
                    $q->whereNull('am_out_time')->orWhereNull('pm_out_time');
                })
                ->first();

            if (! $log) {
                return response()->json([
                    'success' => false,
                    'message' => 'Incomplete attendance record not found or already completed.',
                ]);
            }

            // Store the proof photo
            $photoPath = $request->file('photo_out')->store('attendance-photos', 'public');

            // Determine recovery type: "AM Out" recovery or "PM Out" recovery
            $isAmRecovery = $log->am_in_time && !$log->am_out_time;
            
            $isWholeDay = $request->has('whole_day') && $request->input('whole_day') === 'on' && $isAmRecovery;

            if ($isAmRecovery) {
                // Recovering the Morning Shift
                $timeIn = $log->work_date->setTimeFromTimeString($log->am_in_time)->setTimezone('Asia/Manila');
                $timeOut = $log->work_date->setTimeFromTimeString($request->time_out)->setTimezone('Asia/Manila');
                
                if ($timeOut->lt($timeIn)) {
                     return response()->json(['success' => false, 'message' => 'Morning Time Out cannot be before Morning Time In.']);
                }

                $amMinutes = max(0, $timeIn->diffInMinutes($timeOut));
                $pmMinutes = 0;
                $updates = [
                    'am_out_time' => $request->time_out,
                    'am_out_photo' => $photoPath,
                    'am_out_lat' => $request->input('lat_out'),
                    'am_out_lng' => $request->input('lng_out'),
                    'status' => 'pending',
                    'is_recovered' => true,
                    'recovery_reason' => $request->reason,
                    'recovery_approved' => null,
                ];

                // --- WHOLE DAY RECOVERY LOGIC ---
                if ($isWholeDay) {
                    // Validate that PM In is not already set (safety check)
                    if($log->pm_in_time) {
                         return response()->json(['success' => false, 'message' => 'Cannot overwrite existing PM logs using Whole Day Recovery.']);
                    }

                    $pmInTime = $log->work_date->setTimeFromTimeString($request->pm_in)->setTimezone('Asia/Manila');
                    $pmOutTime = $log->work_date->setTimeFromTimeString($request->pm_out)->setTimezone('Asia/Manila');
                    
                    if ($pmOutTime->lt($pmInTime)) {
                        return response()->json(['success' => false, 'message' => 'Afternoon Time Out cannot be before Afternoon Time In.']);
                    }

                    $pmMinutes = max(0, $pmInTime->diffInMinutes($pmOutTime));

                    // Add PM Data to updates
                    $updates['pm_in_time'] = $request->pm_in;
                    $updates['pm_out_time'] = $request->pm_out;
                    
                    // --- DUPLICATE PHOTO PROOF ---
                    // Save the SAME photo to PM slots so the UI shows it everywhere
                    $updates['pm_in_photo'] = $photoPath;
                    $updates['pm_out_photo'] = $photoPath;
                    
                    // Optional: Duplicate location if available
                    if ($request->input('lat_out')) {
                         $updates['pm_in_lat'] = $request->input('lat_out');
                         $updates['pm_in_lng'] = $request->input('lng_out');
                         $updates['pm_out_lat'] = $request->input('lat_out');
                         $updates['pm_out_lng'] = $request->input('lng_out');
                    }
                }

                $totalMinutes = $amMinutes + $pmMinutes;
                $updates['minutes_worked'] = $totalMinutes;
                $updates['overtime_minutes'] = max(0, $totalMinutes - 480); // Calc overtime if whole day exceeds 8h

                $log->update($updates);
                
                $minutes = $totalMinutes;
            } else {
                // Recovering PM Shift (Missing PM OUT)
                // Note: Whole Day checkbox is ignored here as per logic (only available if stuck in AM)
                
                $timeInStr = $log->pm_in_time;
                if (!$timeInStr) {
                    return response()->json(['success' => false, 'message' => 'Cannot recover PM time without a PM Time In.']);
                }
                
                $timeIn = $log->work_date->setTimeFromTimeString($timeInStr)->setTimezone('Asia/Manila');
                $timeOut = $log->work_date->setTimeFromTimeString($request->time_out)->setTimezone('Asia/Manila');

                if ($timeOut->lt($timeIn)) {
                     return response()->json(['success' => false, 'message' => 'Time out cannot be before Time in.']);
                }
                
                $pmMinutes = max(0, $timeIn->diffInMinutes($timeOut));
                
                // Add AM shift if it exists
                $amMinutes = 0;
                if ($log->am_in_time && $log->am_out_time) {
                    $amStart = \Carbon\Carbon::parse($log->am_in_time);
                    $amEnd = \Carbon\Carbon::parse($log->am_out_time);
                    $amMinutes = max(0, $amStart->diffInMinutes($amEnd));
                }
                
                $totalMinutes = $amMinutes + $pmMinutes;
                
                $log->update([
                    'pm_out_time' => $request->time_out,
                    'pm_out_photo' => $photoPath,
                    'pm_out_lat' => $request->input('lat_out'),
                    'pm_out_lng' => $request->input('lng_out'),
                    'minutes_worked' => $totalMinutes,
                    'overtime_minutes' => max(0, $totalMinutes - 480),
                    'status' => 'pending',
                    'is_recovered' => true,
                    'recovery_reason' => $request->reason,
                    'recovery_approved' => null,
                ]);
                
                $minutes = $totalMinutes; 
            }

            // Log the recovery action for audit purposes
            \Log::info('Attendance recovery completed', [
                'user_id' => $user->id,
                'log_id' => $log->id,
                'type' => $isAmRecovery ? ($isWholeDay ? 'WHOLE_DAY_RECOVERY' : 'AM_RECOVERY') : 'PM_RECOVERY',
                'minutes_worked' => $minutes,
                'reason' => $request->reason,
            ]);

            // Check if student has completed required hours and auto-update status
            $this->checkAndUpdateCompletionStatus($user);

            return response()->json([
                'success' => true,
                'message' => 'Recovery submitted successfully! Your attendance is now pending supervisor approval.',
                'minutes_worked' => $minutes,
                'hours_worked' => round($minutes / 60, 1),
            ]);
        } catch (\Exception $e) {
            \Log::error('Recovery attendance error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to complete attendance: '.$e->getMessage(),
            ]);
        }
    }

    /**
     * Check if student has completed required hours and auto-update status to 'completed'
     */
    protected function checkAndUpdateCompletionStatus($user)
    {
        try {
            $studentProfile = $user->studentProfile;

            if (! $studentProfile || $studentProfile->ojt_status !== 'active') {
                return; // Only check for active students
            }

            // Calculate total hours completed (exclude pending recovered logs)
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

            // Get required hours from acceptance letter or student profile
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
