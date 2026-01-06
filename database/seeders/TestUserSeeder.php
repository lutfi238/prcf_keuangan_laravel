<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        User::updateOrCreate(
            ['email' => 'admin@prcf.id'],
            [
                'nama' => 'Admin PRCF',
                'no_HP' => '081234567890',
                'password' => Hash::make('password'),
                'role' => UserRole::Admin,
                'status' => 'active',
            ]
        );

        // Finance Manager
        User::updateOrCreate(
            ['email' => 'fm@prcf.id'],
            [
                'nama' => 'Finance Manager',
                'no_HP' => '081234567891',
                'password' => Hash::make('password'),
                'role' => UserRole::FinanceManager,
                'status' => 'active',
            ]
        );

        // Staff Accountant
        User::updateOrCreate(
            ['email' => 'sa@prcf.id'],
            [
                'nama' => 'Staff Accountant',
                'no_HP' => '081234567892',
                'password' => Hash::make('password'),
                'role' => UserRole::StaffAccountant,
                'status' => 'active',
            ]
        );

        // Program Manager
        User::updateOrCreate(
            ['email' => 'pm@prcf.id'],
            [
                'nama' => 'Program Manager',
                'no_HP' => '081234567893',
                'password' => Hash::make('password'),
                'role' => UserRole::ProjectManager,
                'status' => 'active',
            ]
        );

        // Direktur
        User::updateOrCreate(
            ['email' => 'dir@prcf.id'],
            [
                'nama' => 'Direktur PRCF',
                'no_HP' => '081234567894',
                'password' => Hash::make('password'),
                'role' => UserRole::Direktur,
                'status' => 'active',
            ]
        );

        $this->command->info('Test users created successfully!');
        $this->command->table(
            ['Email', 'Role', 'Password'],
            [
                ['admin@prcf.id', 'Admin', 'password'],
                ['fm@prcf.id', 'Finance Manager', 'password'],
                ['sa@prcf.id', 'Staff Accountant', 'password'],
                ['pm@prcf.id', 'Program Manager', 'password'],
                ['dir@prcf.id', 'Direktur', 'password'],
            ]
        );
    }
}
