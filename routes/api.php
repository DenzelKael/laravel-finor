<?php

use App\Http\Controllers\Api\PlanController as ApiPlanController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PlanController;
use Illuminate\Support\Facades\Route;

Route::get('/ping', fn () => response()->json([
    'status' => 'ok',
]))->name('api.ping');

Route::middleware(['web', 'auth'])->group(function () {
    /*
     * Dashboard
     */
    Route::get('/chart-data', [DashboardController::class, 'chartData'])
        ->middleware('permission:payments.view')
        ->name('dashboard.chart');

    /*
     * Clients API
     */
    Route::controller(ClientController::class)
        ->prefix('clients')
        ->name('api.clients.')
        ->group(function () {
            Route::get('/', 'index')
                ->middleware('permission:clients.view')
                ->name('index');

            Route::post('/', 'store')
                ->middleware('permission:clients.create')
                ->name('store');

            Route::get('/{client}', 'show')
                ->middleware('permission:clients.view')
                ->name('show');

            Route::put('/{client}', 'update')
                ->middleware('permission:clients.update')
                ->name('update');

            Route::delete('/{client}', 'destroy')
                ->middleware('permission:clients.delete')
                ->name('destroy');
        });

    /*
     * Plans API
     */
    Route::controller(ApiPlanController::class)
        ->prefix('plans')
        ->name('api.plans.')
        ->group(function () {
            Route::get('/', 'index')
                ->middleware('permission:plans.view')
                ->name('index');

            Route::get('/{plan}', 'show')
                ->middleware('permission:plans.view')
                ->name('show');
        });

    Route::controller(PlanController::class)
        ->prefix('plans')
        ->name('api.plans.')
        ->group(function () {
            Route::post('/', 'store')
                ->middleware('permission:plans.create')
                ->name('store');

            Route::put('/{plan}', 'update')
                ->middleware('permission:plans.update')
                ->name('update');

            Route::delete('/{plan}', 'destroy')
                ->middleware('permission:plans.delete')
                ->name('destroy');
        });

    /*
     * Payments API
     */
    Route::controller(PaymentController::class)
        ->prefix('payments')
        ->name('api.payments.')
        ->group(function () {
            Route::get('/', 'index')
                ->middleware('permission:payments.view')
                ->name('index');

            Route::post('/', 'store')
                ->middleware('permission:payments.create')
                ->name('store');

            Route::get(
                '/subscriptions/{subscription}/history',
                'historyBySubscription'
            )
                ->middleware('permission:payments.view')
                ->name('history.subscription');

            Route::get(
                '/clients/{client}/history',
                'historyByClient'
            )
                ->middleware('permission:payments.view')
                ->name('history.client');

            Route::get('/{payment}', 'show')
                ->middleware('permission:payments.view')
                ->name('show');

            Route::patch('/{payment}/cancel', 'cancel')
                ->middleware('permission:payments.cancel')
                ->name('cancel');
        });
});
