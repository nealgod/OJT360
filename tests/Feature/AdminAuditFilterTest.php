<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuditFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_audit_role_filter_returns_only_selected_role()
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
        $student = User::create([
            'name' => 'Intern John',
            'email' => 'student@example.com',
            'password' => Hash::make('password'),
            'role' => 'intern',
            'email_verified_at' => now(),
        ]);
        $coordinator = User::create([
            'name' => 'Coord',
            'email' => 'coord@example.com',
            'password' => Hash::make('password'),
            'role' => 'coordinator',
            'email_verified_at' => now(),
        ]);

        \App\Models\AuditLog::create([
            'user_id' => $student->id,
            'action' => 'login',
            'description' => 's',
        ]);
        \App\Models\AuditLog::create([
            'user_id' => $coordinator->id,
            'action' => 'login',
            'description' => 'c',
        ]);

        $this->actingAs($admin);
        $resp = $this->get(route('admin.audit.index', ['role' => 'coordinator']));
        $resp->assertOk();
        $resp->assertSee('Coordinator');
    }
}
