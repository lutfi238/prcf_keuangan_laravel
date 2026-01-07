<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin user
        User::create([
            'nama' => 'Administrator',
            'email' => 'admin@prcf.id',
            'password' => Hash::make('password123'),
            'role' => UserRole::Admin,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Create Finance Manager
        User::create([
            'nama' => 'Finance Manager',
            'email' => 'fm@prcf.id',
            'password' => Hash::make('password123'),
            'role' => UserRole::FinanceManager,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Create Director
        User::create([
            'nama' => 'Direktur PRCF',
            'email' => 'direktur@prcf.id',
            'password' => Hash::make('password123'),
            'role' => UserRole::Direktur,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Create Project Manager
        User::create([
            'nama' => 'Project Manager',
            'email' => 'pm@prcf.id',
            'password' => Hash::make('password123'),
            'role' => UserRole::ProjectManager,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Create Staff Accountant
        User::create([
            'nama' => 'Staff Accountant',
            'email' => 'sa@prcf.id',
            'password' => Hash::make('password123'),
            'role' => UserRole::StaffAccountant,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Create additional test users
        User::create([
            'nama' => 'PM Kalimantan',
            'email' => 'pm.kalimantan@prcf.id',
            'password' => Hash::make('password123'),
            'role' => UserRole::ProjectManager,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        User::create([
            'nama' => 'SA Junior',
            'email' => 'sa.junior@prcf.id',
            'password' => Hash::make('password123'),
            'role' => UserRole::StaffAccountant,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $this->command->info('Created 7 users with roles: Admin, FM, Direktur, PM (2), SA (2)');
        $this->command->info('Default password for all users: password123');
    }
}