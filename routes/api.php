<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {
    Route::controller(DashboardController::class)
        ->prefix('chart-data')
        ->name('api.chart-data.')
        ->group(function () {
            Route::get('/', 'chartData')
                ->middleware('permission:payments.view')
                ->name('index');
        });
});
