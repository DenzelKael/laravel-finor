<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PlanController;
use Illuminate\Support\Facades\Route;

// AdminLTE authentication routes
Route::middleware('guest')->group(function () {
    Route::get('login', [\App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [\App\Http\Controllers\Auth\LoginController::class, 'login']);

    Route::get('register', [\App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [\App\Http\Controllers\Auth\RegisterController::class, 'register']);

    Route::get('forgot-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('forgot-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

    Route::get('reset-password/{token}', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('reset-password', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/api/chart-data', [DashboardController::class, 'chartData']);

    Route::controller(ClientController::class)
        ->name('clients.')
        ->group(function () {
            Route::get('clients', 'index')->name('index');
            Route::get('clients/create', 'create')->name('create');
            Route::post('clients', 'store')->name('store');
            Route::get('clients/{client}/edit', 'edit')->name('edit');
            Route::put('clients/{client}', 'update')->name('update');
            Route::delete('clients/{client}', 'destroy')->name('destroy');
        });
    Route::resource('plans', \App\Http\Controllers\PlanController::class);

    Route::controller(PaymentController::class)
        ->prefix('payments')
        ->name('payments.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{payment}', 'show')->name('show');
            Route::get('/{payment}/receipt', 'receipt')->name('receipt');
            Route::patch('/{payment}/cancel', 'cancel')->name('cancel');
        });

    // Email verification — protect app routes with the `verified` middleware once
    // your User model implements MustVerifyEmail (adminlte:make-auth wires it in).
    Route::get('email/verify', [\App\Http\Controllers\Auth\EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::get('email/verify/{id}/{hash}', [\App\Http\Controllers\Auth\EmailVerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    Route::post('email/verification-notification', [\App\Http\Controllers\Auth\EmailVerificationController::class, 'send'])
        ->middleware('throttle:6,1')->name('verification.send');

    // Password confirmation — guard sensitive actions with the `password.confirm` middleware.
    Route::get('confirm-password', [\App\Http\Controllers\Auth\ConfirmablePasswordController::class, 'show'])->name('password.confirm');
    Route::post('confirm-password', [\App\Http\Controllers\Auth\ConfirmablePasswordController::class, 'store']);

    Route::post('logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
});
