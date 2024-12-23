<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Client;
use App\Models\Currency;
use App\Models\Vacancy;
use App\Services\CurrencyService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class MoneyInPipelineTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @throws Exception
     */
    public function test_money_in_pipeline_calculation()
    {

        // Create a client
        $client = Client::factory()->create();

        try {
            // Fetch two existing currencies
            $currencyUSD = Currency::where('currency_code', 'USD')->firstOrFail();
            $currencyEUR = Currency::where('currency_code', 'EUR')->firstOrFail();
        } catch (ModelNotFoundException) {
            // Update currencies if none are found
            $currencyService = app(CurrencyService::class);
            $currencyService->updateCurrenciesIfStale();

            // Retry fetching the currencies
            $currencyUSD = Currency::where('currency_code', 'USD')->firstOrFail();
            $currencyEUR = Currency::where('currency_code', 'EUR')->firstOrFail();
        }

        // Create two vacancies with different currencies
        $vacancy1 = Vacancy::factory()->create([
            'client_id' => $client->id,
            'positions' => 3,
            'currency_id' => $currencyUSD->id,
            'remuneration' => 0, // Not used in calculation for the pipeline
            'status' => 'open',
        ]);

        $vacancy2 = Vacancy::factory()->create([
            'client_id' => $client->id,
            'positions' => 5,
            'currency_id' => $currencyEUR->id,
            'remuneration' => 0,
            'status' => 'open',
        ]);

        // Create applications for the vacancies
        Application::factory()->count(3)->create([
            'vacancy_id' => $vacancy1->id,
            'asking_remuneration' => 5000, // USD
        ]);

        Application::factory()->count(4)->create([
            'vacancy_id' => $vacancy2->id,
            'asking_remuneration' => 4000, // EUR
        ]);

        // Call the pipeline report endpoint
        $response = $this->getJson('/api/report/money-in-pipeline');

        // Calculate the expected pipeline commission
        // Vacancy 1: 3 positions * avg(5000) * 10% * rate_to_usd (1)
        $vacancy1Commission = 3 * 5000 * 0.1 * $currencyUSD->rate_to_usd;

        // Vacancy 2: 5 positions * avg(4000) * 10% * rate_to_usd (1.2)
        $vacancy2Commission = 5 * 4000 * 0.1 * $currencyEUR->rate_to_usd;

        $expectedPipeline = $vacancy1Commission + $vacancy2Commission;

        // Assert the response
        $response->assertStatus(200);
        $response->assertJson([
            'total_pipeline_commission_usd' => $expectedPipeline,
        ]);

        // Verify the calculated pipeline matches
        $this->assertEquals(
            $expectedPipeline,
            $response->json('total_pipeline_commission_usd'),
            'Pipeline commission calculation mismatch.'
        );
    }


    public function test_currency_service_failure()
    {
        $mockCurrencyService = Mockery::mock(CurrencyService::class);
        $mockCurrencyService->shouldReceive('updateCurrenciesIfStale')
            ->andThrow(new \Exception('Currency update failed.'));
        $this->app->instance(CurrencyService::class, $mockCurrencyService);

        $response = $this->getJson('/api/report/money-in-pipeline');

        $response->assertStatus(500)
            ->assertJson(['error' => 'Currency update failed.']);
    }

    public function test_no_open_vacancies()
    {
        $response = $this->getJson('/api/report/money-in-pipeline');

        $response->assertOk()
            ->assertJson(['total_pipeline_commission_usd' => 0]);
    }
}
