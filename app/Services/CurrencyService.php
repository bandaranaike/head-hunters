<?php

namespace App\Services;

use App\Models\Currency;
use App\Services\Clients\CurrencyApiClient;
use Carbon\Carbon;
use Exception;

class CurrencyService
{

    protected CurrencyApiClient $apiClient;

    public function __construct(CurrencyApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * Fetch and update currencies if older than 5 minutes.
     * @throws Exception
     */
    public function updateCurrenciesIfStale(): void
    {
        $lastUpdate = Currency::max('last_updated');

        // Check if records are updated or not
        if ($lastUpdate && Carbon::parse($lastUpdate)->diffInSeconds(now()) < Currency::CURRENCY_LIST_UPDATE_FREQUENCY_IN_SECONDS) {
            return; // Has updated. No need to update
        }

        $currencies = $this->apiClient->getCurrencies();
        $rates = $this->apiClient->getRates();

        if ($currencies && $rates) {
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
            throw new Exception('Failed to fetch currencies or rates from API.');
        }
    }
}
