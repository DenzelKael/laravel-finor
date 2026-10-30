<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PlanController;
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

    Route::prefix('v1')
        ->name('api.v1.')
        ->group(function () {
            Route::controller(ClientController::class)
                ->prefix('clients')
                ->name('clients.')
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

            Route::controller(PlanController::class)
                ->prefix('plans')
                ->name('plans.')
                ->group(function () {
                    Route::get('/', 'index')
                        ->middleware('permission:plans.view')
                        ->name('index');

                    Route::post('/', 'store')
                        ->middleware('permission:plans.create')
                        ->name('store');

                    Route::get('/{plan}', 'show')
                        ->middleware('permission:plans.view')
                        ->name('show');

                    Route::put('/{plan}', 'update')
                        ->middleware('permission:plans.update')
                        ->name('update');

                    Route::delete('/{plan}', 'destroy')
                        ->middleware('permission:plans.delete')
                        ->name('destroy');
                });

            Route::controller(PaymentController::class)
                ->prefix('payments')
                ->name('payments.')
                ->group(function () {
                    Route::get('/', 'index')
                        ->middleware('permission:payments.view')
                        ->name('index');

                    Route::post('/', 'store')
                        ->middleware('permission:payments.create')
                        ->name('store');

                    Route::get('/{payment}', 'show')
                        ->middleware('permission:payments.view')
                        ->name('show');

                    Route::patch('/{payment}/cancel', 'cancel')
                        ->middleware('permission:payments.cancel')
                        ->name('cancel');

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
                });
        });
});
