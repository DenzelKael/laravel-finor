<?php

namespace App\Policies;

use App\Models\Subscription;
use App\Models\User;

class SubscriptionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('subscriptions.view');
    }

    public function view(User $user, Subscription $subscription): bool
    {
        return $user->can('subscriptions.view');
    }

    public function create(User $user): bool
    {
        return $user->can('subscriptions.create');
    }

    public function renew(User $user, Subscription $subscription): bool
    {
        return $user->can('subscriptions.renew')
            && $subscription->status->value !== 'CANCELLED';
    }

    public function cancel(User $user, Subscription $subscription): bool
    {
        return $user->can('subscriptions.cancel')
            && $subscription->status->value !== 'CANCELLED';
    }
}