<?php

use App\Http\Controllers\Api\PlanController;
use Illuminate\Support\Facades\Route;

Route::get('/ping', fn () => response()->json(['status' => 'ok']))->name('api.ping');

Route::controller(PlanController::class)
    ->prefix('plans')
    ->name('api.plans.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{plan}', 'show')->name('show');
    });
