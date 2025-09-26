<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user if it doesn't exist
        $admin = User::firstOrCreate(
            ['email' => 'ojt3604dmin@gmail.com'],
            [
                'name' => 'OJT360 Admin',
                'password' => Hash::make('12345678'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        if ($admin->wasRecentlyCreated) {
            $this->command->info('Admin user created successfully!');
        } else {
            $this->command->info('Admin user already exists!');
        }
        
        $this->command->info('Email: ojt3604dmin@gmail.com');
        $this->command->info('Password: 12345678');
    }
}
