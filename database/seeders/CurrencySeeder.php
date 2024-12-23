<?php

namespace Database\Seeders;

use App\Services\CurrencyService;
use Exception;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    protected CurrencyService $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            $this->currencyService->updateCurrenciesIfStale();
            $this->command->info('Currencies and rates have been successfully updated.');
        } catch (Exception $e) {
            $this->command->error($e->getMessage());
        }
    }
}
