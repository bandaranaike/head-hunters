<?php

namespace App\Services;

use App\Models\Currency;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class CurrencyService
{
    /**
     * Fetch and update currencies and rates.
     */
    public function fetchAndUpdateCurrencies(): void
    {
        $lastUpdate = Currency::max('last_updated');

        // Check if records are updated or not
        if ($lastUpdate && Carbon::parse($lastUpdate)->diffInSeconds(now()) < Currency::CURRENCY_LIST_UPDATE_FREQUENCY_IN_SECONDS) {
            return; // Has updated. No need to update
        }

        $currenciesResponse = Http::get('https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies.json');
        $ratesResponse = Http::get('https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies/usd.json');

        if ($currenciesResponse->ok() && $ratesResponse->ok()) {
            $currencies = $currenciesResponse->json();
            $rates = $ratesResponse->json();
            $rateToUsd = $rates['usd'] ?? [];

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
            throw new \Exception('Failed to fetch currencies or rates from API.');
        }
    }
}
