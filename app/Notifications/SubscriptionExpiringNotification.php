<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SubscriptionExpiringNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Subscription $subscription
    ) {}

    /**
     * Define los canales de envío de la notificación.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Información que se almacenará en la base de datos.
     */
    public function toDatabase(object $notifiable): array
    {
        $expirationDate = $this->subscription->expiration_date;

        $daysRemaining = (int) today()->diffInDays(
            $expirationDate,
            false
        );

        return [
            'subscription_id' => $this->subscription->id,
            'client_name' => $this->subscription->client->name,
            'plan_name' => $this->subscription->plan->nombre,
            'expiration_date' => $expirationDate->toDateString(),
            'days_remaining' => $daysRemaining,
            'message' => "La suscripción del cliente {$this->subscription->client->name} está próxima a vencer.",
        ];
    }
}
