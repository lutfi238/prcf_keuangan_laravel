<?php

namespace Database\Seeders;

use App\Models\Village;
use Illuminate\Database\Seeder;

class VillageSeeder extends Seeder
{
    public function run(): void
    {
        $villages = [
            // Kalimantan Barat villages
            ['village_code' => 'V001', 'village_name' => 'Menua Sadap', 'village_abbr' => 'MS', 'description' => 'Kec. Embaloh Hulu, Kab. Kapuas Hulu, Kalimantan Barat'],
            ['village_code' => 'V002', 'village_name' => 'Sungai Uluk', 'village_abbr' => 'SU', 'description' => 'Kec. Embaloh Hulu, Kab. Kapuas Hulu, Kalimantan Barat'],
            ['village_code' => 'V003', 'village_name' => 'Sadap', 'village_abbr' => 'SD', 'description' => 'Kec. Embaloh Hulu, Kab. Kapuas Hulu, Kalimantan Barat'],
            ['village_code' => 'V004', 'village_name' => 'Keluin', 'village_abbr' => 'KL', 'description' => 'Kec. Embaloh Hulu, Kab. Kapuas Hulu, Kalimantan Barat'],
            ['village_code' => 'V005', 'village_name' => 'Pulau Manak', 'village_abbr' => 'PM', 'description' => 'Kec. Embaloh Hulu, Kab. Kapuas Hulu, Kalimantan Barat'],
            ['village_code' => 'V006', 'village_name' => 'Banua Ujung', 'village_abbr' => 'BU', 'description' => 'Kec. Embaloh Hilir, Kab. Kapuas Hulu, Kalimantan Barat'],
            ['village_code' => 'V007', 'village_name' => 'Batu Lintang', 'village_abbr' => 'BL', 'description' => 'Kec. Embaloh Hilir, Kab. Kapuas Hulu, Kalimantan Barat'],
            ['village_code' => 'V008', 'village_name' => 'Nanga Lauk', 'village_abbr' => 'NL', 'description' => 'Kec. Embaloh Hilir, Kab. Kapuas Hulu, Kalimantan Barat'],
            ['village_code' => 'V009', 'village_name' => 'Benuis', 'village_abbr' => 'BN', 'description' => 'Kec. Batang Lupar, Kab. Kapuas Hulu, Kalimantan Barat'],
            ['village_code' => 'V010', 'village_name' => 'Mensiau', 'village_abbr' => 'MI', 'description' => 'Kec. Batang Lupar, Kab. Kapuas Hulu, Kalimantan Barat'],
            
            // Kalimantan Utara villages  
            ['village_code' => 'V011', 'village_name' => 'Long Alango', 'village_abbr' => 'LA', 'description' => 'Kec. Bahau Hulu, Kab. Malinau, Kalimantan Utara'],
            ['village_code' => 'V012', 'village_name' => 'Long Kemuat', 'village_abbr' => 'LK', 'description' => 'Kec. Bahau Hulu, Kab. Malinau, Kalimantan Utara'],
            ['village_code' => 'V013', 'village_name' => 'Long Berini', 'village_abbr' => 'LB', 'description' => 'Kec. Bahau Hulu, Kab. Malinau, Kalimantan Utara'],
            ['village_code' => 'V014', 'village_name' => 'Long Tebulo', 'village_abbr' => 'LT', 'description' => 'Kec. Bahau Hulu, Kab. Malinau, Kalimantan Utara'],
            ['village_code' => 'V015', 'village_name' => 'Apau Ping', 'village_abbr' => 'AP', 'description' => 'Kec. Kayan Hulu, Kab. Malinau, Kalimantan Utara'],
            
            // Papua Barat Daya villages
            ['village_code' => 'V016', 'village_name' => 'Sauwandarek', 'village_abbr' => 'SW', 'description' => 'Kec. Meos Mansar, Kab. Raja Ampat, Papua Barat Daya'],
            ['village_code' => 'V017', 'village_name' => 'Arborek', 'village_abbr' => 'AR', 'description' => 'Kec. Meos Mansar, Kab. Raja Ampat, Papua Barat Daya'],
            ['village_code' => 'V018', 'village_name' => 'Friwen', 'village_abbr' => 'FR', 'description' => 'Kec. Meos Mansar, Kab. Raja Ampat, Papua Barat Daya'],
            ['village_code' => 'V019', 'village_name' => 'Yenbuba', 'village_abbr' => 'YB', 'description' => 'Kec. Meos Mansar, Kab. Raja Ampat, Papua Barat Daya'],
            ['village_code' => 'V020', 'village_name' => 'Saporkren', 'village_abbr' => 'SK', 'description' => 'Kec. Waigeo Selatan, Kab. Raja Ampat, Papua Barat Daya'],
        ];

        foreach ($villages as $village) {
            Village::create($village);
        }

        $this->command->info('Created ' . count($villages) . ' villages across Kalimantan and Papua');
    }
}