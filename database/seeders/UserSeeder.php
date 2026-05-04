<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin account
        User::updateOrCreate(
            ['email' => 'admin@realms.com'],
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'role' => 'admin',
                'contact_number' => '09123456789',
                'is_approved' => 1,
                'email_verified_at' => now(),
                'password' => Hash::make('admin123'),
            ]
        );

        // Tenant account
        User::updateOrCreate(
            ['email' => 'tenant@test.com'],
            [
                'first_name' => 'Test',
                'last_name' => 'Tenant',
                'role' => 'tenant',
                'contact_number' => '09123456789',
                'is_approved' => 1,
                'email_verified_at' => now(),
                'password' => Hash::make('tenant123'),
            ]
        );
    }
}


