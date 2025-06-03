<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@kantor.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'jabatan' => 'Administrator Sistem',
            'divisi' => 'IT',
            'is_active' => true,
        ]);

        // Manager user
        User::create([
            'name' => 'Manager Umum',
            'email' => 'manager@kantor.com',
            'password' => Hash::make('password123'),
            'role' => 'manager',
            'jabatan' => 'Manager',
            'divisi' => 'Umum',
            'is_active' => true,
        ]);

        // Staff users
        User::create([
            'name' => 'John Doe',
            'email' => 'john@kantor.com',
            'password' => Hash::make('password123'),
            'role' => 'staff',
            'jabatan' => 'Staff Administrasi',
            'divisi' => 'Administrasi',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@kantor.com',
            'password' => Hash::make('password123'),
            'role' => 'staff',
            'jabatan' => 'Staff Keuangan',
            'divisi' => 'Keuangan',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Ahmad Hidayat',
            'email' => 'ahmad@kantor.com',
            'password' => Hash::make('password123'),
            'role' => 'staff',
            'jabatan' => 'Staff HR',
            'divisi' => 'Human Resource',
            'is_active' => true,
        ]);

        // Non-active user (untuk testing)
        User::create([
            'name' => 'User Nonaktif',
            'email' => 'nonaktif@kantor.com',
            'password' => Hash::make('password123'),
            'role' => 'staff',
            'jabatan' => 'Staff Test',
            'divisi' => 'Testing',
            'is_active' => false,
        ]);
    }
}