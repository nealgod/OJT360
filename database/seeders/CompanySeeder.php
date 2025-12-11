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

        // BSIT Companies - Real Data
        Company::firstOrCreate(['name' => 'Mac Builders'], [
            'department' => $deptIt?->name ?? 'Department of Computer Studies',
            'address' => 'Purok 8, Brgy Linao, Ormoc City, Leyte 6541 Philippines',
            'contact_person' => null,
            'contact_email' => null,
            'contact_phone' => null,
            'status' => 'active',
        ]);

        Company::firstOrCreate(['name' => 'Planet PC'], [
            'department' => $deptIt?->name ?? 'Department of Computer Studies',
            'address' => 'Real St, Ormoc City, Leyte 6541 Philippines',
            'contact_person' => null,
            'contact_email' => null,
            'contact_phone' => null,
            'status' => 'active',
        ]);

        Company::firstOrCreate(['name' => 'E-Garage'], [
            'department' => $deptIt?->name ?? 'Department of Computer Studies',
            'address' => '12J Navarro Str., Ormoc City, Philippines',
            'contact_person' => null,
            'contact_email' => null,
            'contact_phone' => null,
            'status' => 'active',
        ]);
    }
}
