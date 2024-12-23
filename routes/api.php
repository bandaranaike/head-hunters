<?php

use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/report/money-in-pipeline', [ReportController::class, 'moneyInPipeline']);
