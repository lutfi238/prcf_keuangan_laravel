<?php

namespace Database\Factories;

use App\Enums\ProjectStatus;
use App\Models\Proyek;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Proyek>
 */
class ProyekFactory extends Factory
{
    protected $model = Proyek::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'kode_proyek' => 'PRJ-' . fake()->unique()->numerify('###'),
            'nama_proyek' => fake()->company() . ' Project',
            'status_proyek' => ProjectStatus::Ongoing->value,
            'donor' => fake()->company(),
            'nilai_anggaran' => fake()->randomFloat(2, 10000, 1000000),
            'periode_mulai' => now()->subMonths(6),
            'periode_selesai' => now()->addMonths(12),
            'rekening_khusus' => fake()->bankAccountNumber(),
        ];
    }

    /**
     * Set project as inactive
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_proyek' => ProjectStatus::Cancelled->value,
        ]);
    }
}