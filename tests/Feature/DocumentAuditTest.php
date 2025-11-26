<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\StudentProfile;
use App\Models\DocumentRequirement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class DocumentAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_document_submission_is_logged()
    {
        Storage::fake('public');

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

        $req = DocumentRequirement::create([
            'name' => 'Application Letter',
            'code' => 'application_letter',
            'is_required' => true,
            'status' => 'active',
        ]);

        $file = UploadedFile::fake()->create('letter.pdf', 10, 'application/pdf');

        $this->actingAs($student);
        $this->post(route('documents.submit', $req), [
            'files' => [$file],
        ])->assertRedirect();

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'document_submitted',
            'model_type' => 'StudentDocumentSubmission',
        ]);
    }
}
