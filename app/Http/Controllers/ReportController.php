<?php

namespace App\Http\Controllers;

use App\Models\Vacancy;
use App\Services\CurrencyService;
use Exception;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{

    protected CurrencyService $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    public function moneyInPipeline(): JsonResponse
    {
        try {
            $this->currencyService->updateCurrenciesIfStale();
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }

        $vacancies = Vacancy::with(['applications', 'currency'])
            ->where('status', 'open')
            ->get();

        $pipeline = $vacancies->map(function ($vacancy) {
            $averageRemuneration = $vacancy->applications->avg('asking_remuneration');
            return $vacancy->positions * $averageRemuneration * 0.1 * $vacancy->currency->rate_to_usd;
        });

        $totalPipeline = $pipeline->sum();

        return response()->json(['total_pipeline_commission_usd' => $totalPipeline]);
    }
}
