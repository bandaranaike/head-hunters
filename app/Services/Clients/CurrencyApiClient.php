<?php

namespace App\Services\Clients;

use Illuminate\Support\Facades\Http;

class CurrencyApiClient
{
    protected string $currenciesUrl = 'https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies.json';
    protected string $ratesUrl = 'https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies/usd.json';

    /**
     * Fetch the list of currencies.
     *
     * @return array|null
     */
    public function getCurrencies(): ?array
    {
        $response = Http::get($this->currenciesUrl);

        return $response->ok() ? $response->json() : null;
    }

    /**
     * Fetch currency rates in USD.
     *
     * @return array|null
     */
    public function getRates(): ?array
    {
        $response = Http::get($this->ratesUrl);

        return $response->ok() ? $response->json() : null;
    }
}
