<?php

use App\Http\Controllers\Api\AuthController;
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

Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1')
    ->name('api.login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
    Route::get('/me', [AuthController::class, 'me'])->name('api.me');
});
