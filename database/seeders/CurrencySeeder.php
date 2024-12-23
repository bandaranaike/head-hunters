<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $response = Http::get('https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies.json');

        if ($response->ok()) {
            $currencies = $response->json();

            foreach ($currencies as $code => $name) {
                Currency::updateOrCreate(
                    ['currency_code' => $code],
                    [
                        'currency_name' => $name,
                        'rate_to_usd' => null,
                        'last_updated' => now(),
                    ]
                );
            }
        } else {
            $this->command->error('Failed to fetch currencies from API.');
        }
    }
}
