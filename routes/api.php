<?php

use App\Http\Controllers\SensorController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Greenhouse Monitoring System v2.0
|--------------------------------------------------------------------------
| Routes untuk komunikasi ESP32 dengan Laravel
|
*/

// API Endpoint untuk ESP32 (tanpa autentikasi)
Route::post('/sensor-data', [SensorController::class, 'store'])
    ->name('api.sensor.store');

Route::get('/sensor/latest', [SensorController::class, 'latest'])
    ->name('api.sensor.latest');
