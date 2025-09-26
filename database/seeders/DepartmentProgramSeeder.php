<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Program;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DepartmentProgramSeeder extends Seeder
{
    public function run(): void
    {
        $config = config('departments.departments', []);

        foreach ($config as $deptName => $data) {
            $department = Department::firstOrCreate(
                ['name' => $deptName],
                ['slug' => Str::slug($deptName)]
            );

            $courses = $data['courses'] ?? [];
            foreach ($courses as $programName => $requiredHours) {
                // Ensure program exists and set/update required hours from config
                Program::updateOrCreate(
                    ['department_id' => $department->id, 'name' => $programName],
                    [
                        'slug' => Str::slug($programName),
                        'required_hours' => $requiredHours,
                    ]
                );
            }
        }
    }
}


