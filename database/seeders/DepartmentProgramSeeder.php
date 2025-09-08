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
            foreach (array_keys($courses) as $programName) {
                Program::firstOrCreate(
                    ['department_id' => $department->id, 'name' => $programName],
                    ['slug' => Str::slug($programName)]
                );
            }
        }
    }
}


