<?php
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SubscriptionController;


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
    Route::prefix('admin')->group(function () {
        //users
        Route::get('/users', [UserController::class, 'index'])
            ->middleware('permission:users.view')
            ->name('users.index');

        Route::get('/users/{user}/edit', [UserController::class, 'edit'])
            ->middleware('permission:users.update')
            ->name('users.edit');

        Route::put('/users/{user}', [UserController::class, 'update'])
            ->middleware('permission:users.update')
            ->name('users.update');

        //roles
        Route::get('/roles', [RoleController::class, 'index'])
            ->middleware('permission:roles.view')
            ->name('roles.index');

        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])
            ->middleware('permission:roles.update')
            ->name('roles.edit');

        Route::put('/roles/{role}', [RoleController::class, 'update'])
            ->middleware('permission:roles.update')
            ->name('roles.update');

        //permissions
        Route::get('/permissions', [PermissionController::class, 'index'])
            ->middleware('permission:permissions.view')
            ->name('permissions.index');

        Route::get('/permissions/create', [PermissionController::class, 'create'])
            ->middleware('permission:permissions.create')
            ->name('permissions.create');

        Route::post('/permissions', [PermissionController::class, 'store'])
            ->middleware('permission:permissions.create')
            ->name('permissions.store');

        Route::get('/permissions/{permission}/edit', [PermissionController::class, 'edit'])
            ->middleware('permission:permissions.update')
            ->name('permissions.edit');

        Route::put('/permissions/{permission}', [PermissionController::class, 'update'])
            ->middleware('permission:permissions.update')
            ->name('permissions.update');

        Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])
            ->middleware('permission:permissions.delete')
            ->name('permissions.destroy');
    });

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
    Route::resource('plans', \App\Http\Controllers\PlanController::class)->except('show');

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

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/api/chart-data', [DashboardController::class, 'chartData']);

    Route::post('logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])
        ->name('logout');

    // Rutas explícitas de Suscripciones (Equipo D)
    Route::controller(SubscriptionController::class)
        ->prefix('subscriptions')
        ->name('subscriptions.')
        ->group(function () {
            Route::get('/', 'index')
                ->middleware('permission:subscriptions.view')
                ->name('index');
                
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
                
            Route::patch('/{subscription}/cancel', 'cancel')
                ->middleware('permission:subscriptions.delete')
                ->name('cancel');
        });
});