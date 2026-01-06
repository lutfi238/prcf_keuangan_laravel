<?php

namespace Database\Seeders;

use App\Models\Proyek;
use App\Models\Village;
use App\Models\ProjectCodeBudget;
use App\Enums\ProjectStatus;
use Illuminate\Database\Seeder;

class ProyekSeeder extends Seeder
{
    public function run(): void
    {
        // Get some villages for budget allocation
        $village1 = Village::where('village_abbr', 'MS')->first(); // Menua Sadap
        $village2 = Village::where('village_abbr', 'LA')->first(); // Long Alango
        $village3 = Village::where('village_abbr', 'SW')->first(); // Sauwandarek

        // Project 1: Rainforest Conservation Kalimantan
        $proyek1 = Proyek::create([
            'kode_proyek' => 'RC01',
            'nama_proyek' => 'Rainforest Conservation Kalimantan Barat',
            'donor' => 'USAID',
            'periode_mulai' => '2024-01-01',
            'periode_selesai' => '2027-12-31',
            'status_proyek' => ProjectStatus::Ongoing,
            'nilai_anggaran' => 5000000000, // 5 Milyar
            'rekening_khusus' => 'BRI-123456789',
        ]);

        // Budget codes for RC01
        if ($village1) {
            $this->createBudgetCodes($proyek1->kode_proyek, $village1->id_village, [
                ['exp_code' => '10101', 'budget_usd' => 50000, 'budget_idr' => 800000000],
                ['exp_code' => '10201', 'budget_usd' => 30000, 'budget_idr' => 480000000],
                ['exp_code' => '20101', 'budget_usd' => 25000, 'budget_idr' => 400000000],
            ]);
        }

        // Project 2: Marine Conservation Raja Ampat
        $proyek2 = Proyek::create([
            'kode_proyek' => 'MC02',
            'nama_proyek' => 'Marine Conservation Raja Ampat',
            'donor' => 'WWF International',
            'periode_mulai' => '2025-01-01',
            'periode_selesai' => '2028-12-31',
            'status_proyek' => ProjectStatus::Ongoing,
            'nilai_anggaran' => 3500000000, // 3.5 Milyar
            'rekening_khusus' => 'BRI-987654321',
        ]);

        // Budget codes for MC02
        if ($village3) {
            $this->createBudgetCodes($proyek2->kode_proyek, $village3->id_village, [
                ['exp_code' => '10101', 'budget_usd' => 40000, 'budget_idr' => 640000000],
                ['exp_code' => '10301', 'budget_usd' => 20000, 'budget_idr' => 320000000],
                ['exp_code' => '20201', 'budget_usd' => 15000, 'budget_idr' => 240000000],
            ]);
        }

        // Project 3: Community Forest Management
        $proyek3 = Proyek::create([
            'kode_proyek' => 'CFM03',
            'nama_proyek' => 'Community Forest Management Malinau',
            'donor' => 'Ford Foundation',
            'periode_mulai' => '2023-06-01',
            'periode_selesai' => '2026-05-31',
            'status_proyek' => ProjectStatus::Ongoing,
            'nilai_anggaran' => 2000000000, // 2 Milyar
            'rekening_khusus' => 'BRI-456789123',
        ]);

        // Budget codes for CFM03
        if ($village2) {
            $this->createBudgetCodes($proyek3->kode_proyek, $village2->id_village, [
                ['exp_code' => '10101', 'budget_usd' => 25000, 'budget_idr' => 400000000],
                ['exp_code' => '10201', 'budget_usd' => 15000, 'budget_idr' => 240000000],
            ]);
        }

        $this->command->info('Created 3 projects with budget codes');
    }

    private function createBudgetCodes(string $projectCode, int $villageId, array $budgets): void
    {
        $village = Village::find($villageId);
        $sequence = 1;

        foreach ($budgets as $budget) {
            $placeCode = sprintf('%s-%s-%02d', $budget['exp_code'], $village->village_abbr, $sequence);
            
            ProjectCodeBudget::create([
                'kode_proyek' => $projectCode,
                'id_village' => $villageId,
                'exp_code' => $budget['exp_code'],
                'place_code' => $placeCode,
                'budget_usd' => $budget['budget_usd'],
                'budget_idr' => $budget['budget_idr'],
                'used_usd' => 0,
                'used_idr' => 0,
                'exrate' => 16000,
            ]);
            
            $sequence++;
        }
    }
}