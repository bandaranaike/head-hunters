<?php

use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/pipeline-report', [ReportController::class, 'moneyInPipeline']);
