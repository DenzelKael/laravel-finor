<?php
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PlanController;



// AdminLTE authentication routes
Route::middleware('guest')->group(function () {
    Route::get('login', [\App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [\App\Http\Controllers\Auth\LoginController::class, 'login']);

    // Route::get('register', [\App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
    // Route::post('register', [\App\Http\Controllers\Auth\RegisterController::class, 'register']);

    Route::get('forgot-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('forgot-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

    Route::get('reset-password/{token}', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('reset-password', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');
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
            });

        Route::controller(RoleController::class)
            ->prefix('roles')
            ->name('roles.')
            ->group(function () {
                Route::get('/', 'index')->middleware('permission:roles.view')->name('index');
                Route::get('/{role}/edit', 'edit')->middleware('permission:roles.update')->name('edit');
                Route::put('/{role}', 'update')->middleware('permission:roles.update')->name('update');
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

    Route::controller(ClientController::class)
        ->prefix('clients')
        ->name('clients.')
        ->group(function () {
            Route::get('/', 'index')->middleware('permission:clients.view')->name('index');
            Route::get('/create', 'create')->middleware('permission:clients.create')->name('create');
            Route::post('/', 'store')->middleware('permission:clients.create')->name('store');
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
            Route::get('/{payment}', 'show')->middleware('permission:payments.view')->name('show');
            Route::get('/{payment}/receipt', 'receipt')->middleware('permission:payments.view')->name('receipt');
            Route::patch('/{payment}/cancel', 'cancel')->middleware('permission:payments.cancel')->name('cancel');
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

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/api/chart-data', [DashboardController::class, 'chartData'])
        ->middleware('permission:payments.view');

    Route::post('logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])
        ->name('logout');
});