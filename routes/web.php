<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Greenhouse Monitoring System v2.0
|--------------------------------------------------------------------------
*/

// Halaman Welcome (Landing Page)
Route::get('/', function () {
    return view('welcome');
});

// Route yang memerlukan autentikasi
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
    
    // History
    Route::get('/history', [DashboardController::class, 'history'])
        ->name('history');
    
    // Download CSV
    Route::get('/download-csv', [DashboardController::class, 'downloadCsv'])
        ->name('download.csv');
    
    // Profile Management (dari Laravel Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

// Auth routes (login, register, dll) - dari Breeze
require __DIR__.'/auth.php';
