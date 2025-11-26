<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_and_logout_are_logged()
    {
        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ])->assertRedirect();

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'login',
            'model_type' => 'User',
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);
        $this->post('/logout')->assertRedirect('/');

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'logout',
            'model_type' => 'User',
            'user_id' => $user->id,
        ]);
    }
}
