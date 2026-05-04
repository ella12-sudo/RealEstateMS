<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'first_name' => 'System',
            'last_name' => 'Admin',
            'email' => 'admin@realms.com',
            'contact_number' => '09123456789',
            'role' => 'admin',
            'password' => Hash::make('admin123'),
            'is_approved' => true,
        ]);
    }
}