<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * 
     * Run with: php artisan migrate:fresh --seed
     */
    public function run(): void
    {
        $this->command->info('=== PRCF Keuangan Database Seeder ===');
        $this->command->newLine();

        // 1. Seed Users first (required for foreign keys)
        $this->command->info('Step 1: Seeding Users...');
        $this->call(UserSeeder::class);
        $this->command->newLine();

        // 2. Seed Villages (reference data)
        $this->command->info('Step 2: Seeding Villages...');
        $this->call(VillageSeeder::class);
        $this->command->newLine();

        // 3. Seed Projects with Budget Codes
        $this->command->info('Step 3: Seeding Projects and Budget Codes...');
        $this->call(ProyekSeeder::class);
        $this->command->newLine();

        $this->command->info('=== Seeding Complete! ===');
        $this->command->newLine();
        $this->command->info('Summary:');
        $this->command->info('- Users: 7 (Admin, FM, Direktur, PM x2, SA x2)');
        $this->command->info('- Villages: 20 (Kalimantan & Papua)');
        $this->command->info('- Projects: 3 with budget codes');
        $this->command->newLine();
        $this->command->info('Login credentials: email@prcf.or.id / password123');
    }
}