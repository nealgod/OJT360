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
