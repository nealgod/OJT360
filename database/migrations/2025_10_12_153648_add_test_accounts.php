<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\StudentProfile;
use App\Models\CoordinatorProfile;
use App\Models\Company;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create test students
        $students = [
            [
                'name' => 'Neal Gasal',
                'email' => 'neal.gasal@evsu.edu.ph',
                'student_id' => '2021-001',
                'course' => 'Bachelor of Science in Information Technology',
                'department' => 'Computer Studies',
            ],
            [
                'name' => 'John Doe',
                'email' => 'gasalneal09123@gmail.com',
                'student_id' => '2021-002',
                'course' => 'Bachelor of Science in Computer Science',
                'department' => 'Computer Studies',
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'smurfacc12345x1@gmail.com',
                'student_id' => '2021-003',
                'course' => 'Bachelor of Science in Information Technology',
                'department' => 'Computer Studies',
            ],
        ];

        foreach ($students as $studentData) {
            $user = User::firstOrCreate(
                ['email' => $studentData['email']],
                [
                    'name' => $studentData['name'],
                    'email' => $studentData['email'],
                    'password' => bcrypt('12345678'),
                    'role' => 'intern',
                    'email_verified_at' => now(),
                ]
            );

            // Create student profile if it doesn't exist
            if (!$user->studentProfile) {
                $user->studentProfile()->create([
                    'student_id' => $studentData['student_id'],
                    'course' => $studentData['course'],
                    'department' => $studentData['department'],
                    'phone' => '09123456789',
                    'ojt_status' => 'pending',
                ]);
            }
        }

        // Create test coordinator
        $coordinator = User::firstOrCreate(
            ['email' => 'fireball123x1@gmail.com'],
            [
                'name' => 'OJT Coordinator',
                'email' => 'fireball123x1@gmail.com',
                'password' => bcrypt('12345678'),
                'role' => 'coordinator',
                'email_verified_at' => now(),
            ]
        );

        // Create coordinator profile if it doesn't exist
        if (!$coordinator->coordinatorProfile) {
            $coordinator->coordinatorProfile()->create([
                'department' => 'Computer Studies',
                'program' => 'Information Technology',
                'employee_id' => 'COORD-001',
            ]);
        }

        // Create a test company if it doesn't exist
        $company = Company::firstOrCreate(
            ['name' => 'TechForge Solutions'],
            [
                'name' => 'TechForge Solutions',
                'address' => '123 Tech Street, Tacloban City',
                'contact_person' => 'HR Manager',
                'contact_email' => 'hr@techforge.com',
                'contact_phone' => '09123456789',
                'department' => 'Computer Studies',
                'status' => 'active',
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove test accounts
        $emails = [
            'neal.gasal@evsu.edu.ph',
            'gasalneal09123@gmail.com',
            'smurfacc12345x1@gmail.com',
            'fireball123x1@gmail.com',
        ];

        User::whereIn('email', $emails)->delete();
    }
};