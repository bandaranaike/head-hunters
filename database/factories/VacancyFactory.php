<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vacancy>
 */
class VacancyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'role_id' => Role::factory(),
            'positions' => $this->faker->numberBetween(1, 10),
            'remuneration' => $this->faker->numberBetween(50000, 150000),
            'currency_code' => $this->faker->currencyCode,
            'description' => $this->faker->paragraph,
            'status' => $this->faker->randomElement(['open', 'closed', 'paused']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
