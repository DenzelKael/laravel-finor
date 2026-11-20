<?php

namespace App\Console\Commands;

use App\Enums\RoleName;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\SubscriptionExpiringNotification;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('subscriptions:notify-expiring {--days=3}')]
#[Description('Notifica a los administradores sobre las suscripciones próximas a vencer')]
class NotifyExpiringSubscriptions extends Command
{
    public function handle(): int
    {
        $days = (int) $this->option('days');

        if ($days < 0) {
            $this->error('La cantidad de días no puede ser negativa.');

            return self::FAILURE;
        }

        $subscriptions = Subscription::with(['client', 'plan'])
            ->expiringWithin($days)
            ->get();

        if ($subscriptions->isEmpty()) {
            $this->info('No hay suscripciones próximas a vencer.');

            return self::SUCCESS;
        }

        $admins = User::role(RoleName::Admin->value)->get();

        if ($admins->isEmpty()) {
            $this->warn('No se encontraron usuarios con el rol Admin.');

            return self::SUCCESS;
        }

        $notificationsSent = 0;

        foreach ($admins as $admin) {
            foreach ($subscriptions as $subscription) {
                $expirationDate = $subscription->expiration_date->toDateString();

                $alreadyNotified = $admin->notifications()
                    ->where('type', SubscriptionExpiringNotification::class)
                    ->where('data->subscription_id', $subscription->id)
                    ->where('data->expiration_date', $expirationDate)
                    ->exists();

                if ($alreadyNotified) {
                    continue;
                }

                $admin->notify(
                    new SubscriptionExpiringNotification($subscription)
                );

                $notificationsSent++;
            }
        }

        $this->info(
            "Se enviaron {$notificationsSent} notificaciones nuevas."
        );

        return self::SUCCESS;
    }
}
