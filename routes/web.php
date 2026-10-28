<?php

use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// AdminLTE authentication routes
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);

    // Route::get('register', [\App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
    // Route::post('register', [\App\Http\Controllers\Auth\RegisterController::class, 'register']);

    Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

    Route::get('reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::prefix('admin')->group(function () {

        Route::controller(UserController::class)
            ->prefix('users')
            ->name('users.')
            ->group(function () {
                Route::get('/', 'index')->middleware('permission:users.view')->name('index');
                Route::get('/{user}/edit', 'edit')->middleware('permission:users.update')->name('edit');
                Route::put('/{user}', 'update')->middleware('permission:users.update')->name('update');

                Route::get('/create', 'create')->middleware('permission:users.create')->name('create');
                Route::post('/', 'store')->middleware('permission:users.create')->name('store');

            });

        Route::controller(RoleController::class)
            ->prefix('roles')
            ->name('roles.')
            ->group(function () {
                Route::get('/', 'index')->middleware('permission:roles.view')->name('index');
                Route::get('/create', 'create')->middleware('permission:roles.create')->name('create');
                Route::post('/', 'store')->middleware('permission:roles.create')->name('store');
                Route::get('/{role}/edit', 'edit')->middleware('permission:roles.update')->name('edit');
                Route::put('/{role}', 'update')->middleware('permission:roles.update')->name('update');
                Route::delete('/{role}', 'destroy')->middleware('permission:roles.delete')->name('destroy');
            });

        Route::controller(PermissionController::class)
            ->prefix('permissions')
            ->name('permissions.')
            ->group(function () {
                Route::get('/', 'index')->middleware('permission:permissions.view')->name('index');
                Route::get('/create', 'create')->middleware('permission:permissions.create')->name('create');
                Route::post('/', 'store')->middleware('permission:permissions.create')->name('store');
                Route::get('/{permission}/edit', 'edit')->middleware('permission:permissions.update')->name('edit');
                Route::put('/{permission}', 'update')->middleware('permission:permissions.update')->name('update');
                Route::delete('/{permission}', 'destroy')->middleware('permission:permissions.delete')->name('destroy');
            });
    });

    Route::controller(ProfileController::class)
        ->prefix('profile')
        ->name('profile.')
        ->group(function () {
            Route::get('/', 'edit')->name('edit');
            Route::put('/', 'update')->name('update');
        });

    Route::controller(ClientController::class)
        ->prefix('clients')
        ->name('clients.')
        ->group(function () {
            Route::get('/', 'index')->middleware('permission:clients.view')->name('index');
            Route::get('/create', 'create')->middleware('permission:clients.create')->name('create');
            Route::get('/export', 'export')->middleware('permission:clients.view')->name('export');
            Route::post('/', 'store')->middleware('permission:clients.create')->name('store');
            Route::get('/{client}', 'show')->middleware('permission:clients.view')->name('show');
            Route::get('/{client}/edit', 'edit')->middleware('permission:clients.update')->name('edit');
            Route::put('/{client}', 'update')->middleware('permission:clients.update')->name('update');
            Route::delete('/{client}', 'destroy')->middleware('permission:clients.delete')->name('destroy');
        });

    Route::controller(PlanController::class)
        ->prefix('plans')
        ->name('plans.')
        ->group(function () {
            Route::get('/', 'index')->middleware('permission:plans.view')->name('index');
            Route::get('/create', 'create')->middleware('permission:plans.create')->name('create');
            Route::post('/', 'store')->middleware('permission:plans.create')->name('store');
            Route::get('/{plan}/edit', 'edit')->middleware('permission:plans.update')->name('edit');
            Route::put('/{plan}', 'update')->middleware('permission:plans.update')->name('update');
            Route::delete('/{plan}', 'destroy')->middleware('permission:plans.delete')->name('destroy');

        });
    Route::controller(PaymentController::class)
        ->prefix('payments')
        ->name('payments.')
        ->group(function () {
            Route::get('/', 'index')->middleware('permission:payments.view')->name('index');
            Route::get('/create', 'create')->middleware('permission:payments.create')->name('create');
            Route::post('/', 'store')->middleware('permission:payments.create')->name('store');

            Route::get(
                '/subscription/{subscription}/history',
                'historyBySubscription'
            )->middleware('permission:payments.view')->name('history.subscription');

            Route::get(
                '/client/{client}/history',
                'historyByClient'
            )->middleware('permission:payments.view')->name('history.client');

            Route::get('/{payment}', 'show')->middleware('permission:payments.view')->name('show');
            Route::get('/{payment}/receipt', 'receipt')->middleware('permission:payments.view')->name('receipt');
            Route::patch('/{payment}/cancel', 'cancel')->middleware('permission:payments.cancel')->name('cancel');

            Route::get(
                '/{payment}/receipt/pdf',
                'receiptPdf'
            )->middleware('permission:payments.view')->name('receipt.pdf');
        });

    // Email verification — protect app routes with the `verified` middleware once
    // your User model implements MustVerifyEmail (adminlte:make-auth wires it in).
    Route::get('email/verify', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::get('email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    Route::post('email/verification-notification', [EmailVerificationController::class, 'send'])
        ->middleware('throttle:6,1')->name('verification.send');

    // Password confirmation — guard sensitive actions with the `password.confirm` middleware.
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/api/chart-data', [DashboardController::class, 'chartData'])
        ->middleware('permission:payments.view');

    Route::post('logout', [LoginController::class, 'logout'])
        ->name('logout');
    // Rutas explícitas de Suscripciones (Equipo D)
    Route::controller(SubscriptionController::class)
        ->prefix('subscriptions')
        ->name('subscriptions.')
        ->group(function () {
            Route::get('/', 'index')
                ->middleware('permission:subscriptions.view')
                ->name('index');

            Route::get('/data', 'data')
                ->middleware('permission:subscriptions.view')
                ->name('data');

            Route::get('/create', 'create')
                ->middleware('permission:subscriptions.create')
                ->name('create');

            Route::post('/', 'store')
                ->middleware('permission:subscriptions.create')
                ->name('store');

            Route::get('/{subscription}', 'show')
                ->middleware('permission:subscriptions.view')
                ->name('show');

            Route::patch('/{subscription}/renew', 'renew')
                ->middleware('permission:subscriptions.update')
                ->name('renew');

            // La cancelación de suscripción se mapea a 'subscriptions.delete' siguiendo
            // el esquema estándar de permisos CRUD de Spatie (view, create, update, delete).
            Route::patch('/{subscription}/cancel', 'cancel')
                ->middleware('permission:subscriptions.delete')
                ->name('cancel');
        });
});
