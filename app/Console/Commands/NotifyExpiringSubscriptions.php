<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Notifications\SubscriptionExpiringNotification;
use Illuminate\Console\Command;

class NotifyExpiringSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:notify-expiring {--days=3 : Días de anticipación para el aviso}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Avisa a los clientes cuyas suscripciones activas vencen dentro de N días';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = filter_var($this->option('days'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);

        if ($days === false) {
            $this->error('La opción --days debe ser un número entero mayor o igual a 0.');

            return self::INVALID;
        }

        $notified = 0;

        Subscription::expiringWithin($days)
            ->with(['client', 'plan'])
            ->each(function (Subscription $subscription) use (&$notified) {
                $subscription->client->notify(new SubscriptionExpiringNotification($subscription));
                $notified++;
            });

        $this->info("Se notificaron {$notified} suscripciones por vencer en los próximos {$days} días.");

        return self::SUCCESS;
    }
}
