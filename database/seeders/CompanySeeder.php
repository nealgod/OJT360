<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Department;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        // Map some programs/departments by name for demo; companies table currently stores department as string
        $deptIt = Department::where('name', 'Department of Computer Studies')->first();
        $deptEdu = Department::where('name', 'Department of Teacher Education')->first();
        $deptEng = Department::where('name', 'Department of Engineering')->first();

        Company::firstOrCreate(['name' => 'TechForge Solutions'], [
            'department' => $deptIt?->name ?? 'Department of Computer Studies',
            'address' => '123 Innovation Ave, City',
            'contact_person' => 'Jane Doe',
            'contact_email' => 'hr@techforge.test',
            'contact_phone' => '0917-000-1111',
            'status' => 'active',
        ]);

        Company::firstOrCreate(['name' => 'EduCare Center'], [
            'department' => $deptEdu?->name ?? 'Department of Teacher Education',
            'address' => '45 Learning St, City',
            'contact_person' => 'Mr. Santos',
            'contact_email' => 'contact@educare.test',
            'contact_phone' => '0917-000-2222',
            'status' => 'active',
        ]);

        Company::firstOrCreate(['name' => 'BuildRight Engineering'], [
            'department' => $deptEng?->name ?? 'Department of Engineering',
            'address' => '789 Structure Rd, City',
            'contact_person' => 'Engr. Cruz',
            'contact_email' => 'apply@buildright.test',
            'contact_phone' => '0917-000-3333',
            'status' => 'active',
        ]);
    }
}


