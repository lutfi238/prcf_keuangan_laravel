<?php

namespace Database\Factories;

use App\Models\Village;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Village>
 */
class VillageFactory extends Factory
{
    protected $model = Village::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $abbr = strtoupper(fake()->lexify('???'));
        return [
            'village_code' => 'VIL-' . fake()->unique()->numerify('###'),
            'village_name' => fake()->city(),
            'village_abbr' => $abbr,
            'description' => fake()->sentence(),
        ];
    }
}