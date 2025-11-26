<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\CoordinatorProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CompanyAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_crud_is_logged()
    {
        $coord = User::create([
            'name' => 'Coord',
            'email' => 'coord@example.com',
            'password' => Hash::make('password'),
            'role' => 'coordinator',
            'email_verified_at' => now(),
        ]);
        CoordinatorProfile::create([
            'user_id' => $coord->id,
            'employee_id' => 'EMP001',
            'department' => 'CET',
            'program_id' => null,
            'phone' => '1234567890',
            'status' => 'active',
        ]);

        $this->actingAs($coord);

        $this->post(route('coord.companies.store'), [
            'name' => 'ABC Tech',
            'address' => 'Address',
            'contact_person' => 'John',
            'contact_email' => 'j@abc.com',
            'contact_phone' => '123',
            'status' => 'active',
        ])->assertRedirect();

        $company = Company::where('name', 'ABC Tech')->first();
        $this->assertNotNull($company);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'created',
            'model_type' => 'Company',
            'model_id' => $company->id,
        ]);

        $this->post(route('coord.companies.update', $company), [
            'name' => 'ABC Tech Updated',
            'address' => 'New Address',
            'contact_person' => 'Jane',
            'contact_email' => 'jane@abc.com',
            'contact_phone' => '456',
            'status' => 'active',
        ])->assertRedirect();
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'updated',
            'model_type' => 'Company',
            'model_id' => $company->id,
        ]);

        $this->patch(route('coord.companies.toggle-status', $company))->assertRedirect();
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'updated',
            'model_type' => 'Company',
            'model_id' => $company->id,
        ]);

        $this->delete(route('coord.companies.destroy', $company))->assertRedirect();
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'deleted',
            'model_type' => 'Company',
            'model_id' => $company->id,
        ]);
    }
}
