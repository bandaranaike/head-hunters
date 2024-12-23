<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currenciesResponse = Http::get('https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies.json');
        $ratesResponse = Http::get('https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies/usd.json');

        if ($currenciesResponse->ok() && $ratesResponse->ok()) {
            $currencies = $currenciesResponse->json();
            $rates = $ratesResponse->json();
            $rateToUsd = $rates['usd'] ?? [];

//            dd($rateToUsd);

            $dataToInsert = [];
            foreach ($currencies as $code => $name) {
                $dataToInsert[] = [
                    'currency_code' => $code,
                    'currency_name' => $name,
                    'rate_to_usd' => $rateToUsd[$code] ?? null,
                    'last_updated' => now(),
                ];
            }

            Currency::upsert($dataToInsert, ['currency_code'], ['currency_name', 'rate_to_usd', 'last_updated']);
        } else {
            $this->command->error('Failed to fetch currencies or rates from API.');
        }
    }
}
