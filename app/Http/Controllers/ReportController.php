<?php

namespace App\Http\Controllers;

use App\Models\Vacancy;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    public function moneyInPipeline(): JsonResponse
    {
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
