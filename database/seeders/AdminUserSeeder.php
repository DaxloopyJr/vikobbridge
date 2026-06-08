<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'first_name' => 'System',
            'middle_name' => null,
            'last_name' => 'Administrator',
            'email' => 'admin@vicobridge.com',
            'phone_number' => '+255700000001',
            'gender' => 'male',
            'password' => Hash::make('admin123'),
            'status' => 'active',
            'profile_completed' => true,
            'terms_accepted' => true,
        ]);

        $admin->assignRole('super-admin');
    }
}
