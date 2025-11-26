<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MessageAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_message_send_and_read_are_logged()
    {
        $coord = User::create([
            'name' => 'Coordinator',
            'email' => 'coord@example.com',
            'password' => Hash::make('password'),
            'role' => 'coordinator',
            'email_verified_at' => now(),
        ]);
        \App\Models\CoordinatorProfile::create([
            'user_id' => $coord->id,
            'employee_id' => 'EMP999',
            'department' => 'CET',
            'program_id' => null,
            'phone' => '123',
            'status' => 'active',
        ]);
        $student = User::create([
            'name' => 'Student X',
            'email' => 'studentx@example.com',
            'password' => Hash::make('password'),
            'role' => 'intern',
            'email_verified_at' => now(),
        ]);
        \App\Models\StudentProfile::create([
            'user_id' => $student->id,
            'student_id' => $student->id,
            'department' => 'CET',
            'ojt_status' => 'active',
            'course' => 'BSIT',
            'year_level' => 3,
            'section' => 'A',
        ]);

        $this->actingAs($coord);
        $this->post(route('messages.store'), [
            'recipient_id' => $student->id,
            'subject' => 'Subject',
            'message' => 'Body',
        ])->assertRedirect();

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'message_sent',
            'model_type' => 'Message',
        ]);

        $message = \App\Models\Message::first();
        $this->actingAs($student);
        $this->patch(route('messages.read', $message))->assertRedirect();
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'message_read',
            'model_type' => 'Message',
            'model_id' => $message->id,
        ]);
    }
}
