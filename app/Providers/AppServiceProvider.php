<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use Spatie\Permission\Models\HasRoles;
use Illuminate\Pagination\Paginator;
use App\Enums\RoleName;
use App\Models\Payment;
use App\Policies\PaymentPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        Gate::policy(Payment::class, PaymentPolicy::class);

        Gate::before(function (User $user) {
            return $user->hasRole(RoleName::Admin->value)
                ? true
                : null;
        });
    }
}
