<?php

namespace Tests\Feature;

use App\Models\StudentProfile;
use App\Models\User;
use App\Models\WeeklyReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class WeeklyReportAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_weekly_submit_is_logged()
    {
        $student = User::create([
            'name' => 'Student',
            'email' => 'student@example.com',
            'password' => Hash::make('password'),
            'role' => 'intern',
            'email_verified_at' => now(),
        ]);
        StudentProfile::create([
            'user_id' => $student->id,
            'student_id' => $student->id,
            'ojt_status' => 'active',
            'program_id' => null,
            'department' => 'CET',
            'course' => 'BSIT',
            'year_level' => 3,
            'section' => 'A',
        ]);

        $weekly = WeeklyReport::create([
            'student_user_id' => $student->id,
            'coordinator_user_id' => null,
            'status' => 'draft',
            'week_number' => 1,
            'week_start_date' => now()->startOfWeek(),
            'week_end_date' => now()->endOfWeek(),
            'entries' => [['date' => now()->toDateString(), 'activity' => 'A', 'hours' => 8]],
        ]);

        $this->actingAs($student);
        $this->patch(route('reports.weekly.submit', $weekly))->assertRedirect();

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'weekly_submitted',
            'model_type' => 'WeeklyReport',
            'model_id' => $weekly->id,
        ]);
    }
}
