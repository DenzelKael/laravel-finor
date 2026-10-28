<?php

namespace App\Providers;

use App\Enums\RoleName;
use App\Models\Payment;
use App\Models\User;
use App\Observers\PermissionObserver;
use App\Observers\RoleObserver;
use App\Policies\PaymentPolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Role::observe(RoleObserver::class);
        Permission::observe(PermissionObserver::class);

        Paginator::useBootstrapFive();

        Gate::policy(Payment::class, PaymentPolicy::class);

        Gate::before(function (User $user) {
            return $user->hasRole(RoleName::Admin->value)
                ? true
                : null;
        });
    }
}
