<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Client;
use App\Models\Currency;
use App\Models\Vacancy;
use Tests\TestCase;

class MoneyInPipelineTest extends TestCase
{

    public function test_money_in_pipeline_calculation()
    {


        // Create a client
        $client = Client::factory()->create();

        // Fetch two existing currencies
        $currencyUSD = Currency::where('currency_code', 'USD')->firstOrFail();
        $currencyEUR = Currency::where('currency_code', 'EUR')->firstOrFail();

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
        $response = $this->getJson('/api/pipeline-report');

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
}
