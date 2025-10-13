<?php

namespace Tests\Feature;

use App\Models\AttendanceLog;
use App\Models\PlacementRequest;
use App\Models\StudentProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AttendanceFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_attendance_with_break_and_overtime_is_computed_correctly(): void
    {
        // Create a student with active OJT
        $student = User::factory()->create([
            'role' => 'intern',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        StudentProfile::create([
            'user_id' => $student->id,
            'student_id' => 'S-001',
            'course' => 'BSIT',
            'department' => 'Engineering',
            'ojt_status' => 'active',
        ]);

        $placement = PlacementRequest::create([
            'student_user_id' => $student->id,
            'status' => 'approved',
            'start_date' => Carbon::parse('2025-10-20'),
            'break_minutes' => 60,
            'decided_at' => now(),
        ]);

        // Time-in at 06:50 (early), time-out at 17:30 same day
        Carbon::setTestNow(Carbon::parse('2025-10-20 06:50:00', config('app.timezone')));
        $this->actingAs($student)
            ->post(route('attendance.timeIn'), [
                'photo_in' => UploadedFile::fake()->image('in.jpg', 10, 10),
            ])
            ->assertStatus(302);

        Carbon::setTestNow(Carbon::parse('2025-10-20 17:30:00', config('app.timezone')));
        $this->post(route('attendance.timeOut'), [
            'photo_out' => UploadedFile::fake()->image('out.jpg', 10, 10),
        ])->assertStatus(302);

        $log = AttendanceLog::where('student_user_id', $student->id)
            ->whereDate('work_date', '2025-10-20')
            ->first();

        $this->assertNotNull($log);
        // Total from 06:50 to 17:30 = 10h 40m = 640 minutes; minus 60 = 580
        $this->assertEquals(580, $log->minutes_worked);
    }
}



