<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\StudentProfile;
use App\Models\CoordinatorProfile;
use App\Models\Company;
use Illuminate\Database\Seeder;

class TestAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test students
        $students = [
            [
                'name' => 'Neal Gasal',
                'email' => 'neal.gasal@evsu.edu.ph',
                'student_id' => '2021-001',
                'course' => 'Bachelor of Science in Information Technology (BSIT)',
                'department' => 'Department of Computer Studies',
            ],
            [
                'name' => 'John Doe',
                'email' => 'gasalneal09123@gmail.com',
                'student_id' => '2021-002',
                'course' => 'Bachelor of Science in Information Technology (BSIT)',
                'department' => 'Department of Computer Studies',
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'smurfacc12345x1@gmail.com',
                'student_id' => '2021-003',
                'course' => 'Bachelor of Science in Information Technology (BSIT)',
                'department' => 'Department of Computer Studies',
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
            
            // Ensure the account is always verified
            if (!$user->email_verified_at) {
                $user->update(['email_verified_at' => now()]);
            }

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
        
        // Ensure the coordinator account is always verified
        if (!$coordinator->email_verified_at) {
            $coordinator->update(['email_verified_at' => now()]);
        }

        // Create coordinator profile if it doesn't exist
        if (!$coordinator->coordinatorProfile) {
            // Get the Department of Computer Studies
            $department = \App\Models\Department::where('name', 'Department of Computer Studies')->first();
            $program = \App\Models\Program::where('name', 'Bachelor of Science in Information Technology (BSIT)')->first();
            
            $coordinator->coordinatorProfile()->create([
                'department_id' => $department ? $department->id : null,
                'program_id' => $program ? $program->id : null,
                'department' => 'Department of Computer Studies',
                'program' => 'Bachelor of Science in Information Technology (BSIT)',
                'employee_id' => 'COORD-001',
                'status' => 'active',
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
                'department' => 'Department of Computer Studies',
                'status' => 'active',
            ]
        );
    }
}