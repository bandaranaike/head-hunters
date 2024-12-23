<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Vacancy;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Commission>
 */
class CommissionFactory extends Factory
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
            'vacancy_id' => Vacancy::factory(),
            'total_commission_usd' => $this->faker->randomFloat(2, 100, 5000),
            'calculated_at' => now(),
        ];
    }
}
