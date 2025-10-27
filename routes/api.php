<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Get attendance data for a specific date
Route::get('/attendance/{date}', function (Request $request, $date) {
    try {
        $user = $request->user();
        
        // Validate date format
        $dateObj = \Carbon\Carbon::parse($date);
        
        // Get attendance for the selected date
        $attendance = \App\Models\AttendanceLog::where('student_user_id', $user->id)
            ->whereDate('work_date', $dateObj->format('Y-m-d'))
            ->first();
        
        if ($attendance) {
            return response()->json([
                'success' => true,
                'attendance' => [
                    'time_in' => $attendance->time_in,
                    'time_out' => $attendance->time_out,
                    'time_in_formatted' => $attendance->time_in_formatted,
                    'time_out_formatted' => $attendance->time_out_formatted,
                    'hours_worked_formatted' => $attendance->hours_worked_formatted,
                    'minutes_worked' => $attendance->minutes_worked,
                ]
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'No attendance found for this date'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid date format'
        ], 400);
    }
});
