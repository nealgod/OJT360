<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
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
        $request->validate([
            'photo_in' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);

        $user = Auth::user();
        abort_unless($user->hasActiveOJT(), 403);

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
            'time_in' => now()->format('H:i:s'),
            'photo_in_path' => $path,
            'status' => 'approved',
            'lat_in' => $request->input('lat_in'),
            'lng_in' => $request->input('lng_in'),
        ]);

        return back()->with('success', 'Timed in successfully.');
    }

    public function timeOut(Request $request)
    {
        $request->validate([
            'photo_out' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);

        $user = Auth::user();
        abort_unless($user->hasActiveOJT(), 403);

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

        $timeIn = \Carbon\Carbon::createFromFormat('H:i:s', $log->time_in);
        $timeOut = now();
        $minutes = max(0, $timeIn->diffInMinutes($timeOut));
        $minutes = min($minutes, 8 * 60); // cap at 8 hours for the day

        $log->update([
            'time_out' => $timeOut->format('H:i:s'),
            'photo_out_path' => $path,
            'minutes_worked' => $minutes,
            'lat_out' => $request->input('lat_out'),
            'lng_out' => $request->input('lng_out'),
        ]);

        return back()->with('success', 'Timed out successfully.');
    }
}


