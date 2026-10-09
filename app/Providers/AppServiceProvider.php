<?php

namespace App\Providers;

use App\Enums\RoleName;
use App\Models\Payment;
use App\Models\User;
use App\Policies\PaymentPolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

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