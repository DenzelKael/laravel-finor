<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionExpiringNotification extends Notification
{
    use Queueable;

    public function __construct(public Subscription $subscription) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $expiration = $this->subscription->expiration_date;
        $daysLeft = (int) today()->diffInDays($expiration);

        $when = match ($daysLeft) {
            0 => 'hoy',
            1 => 'mañana',
            default => "en {$daysLeft} días",
        };

        return (new MailMessage)
            ->subject('Tu suscripción está por vencer')
            ->greeting("Hola {$notifiable->name},")
            ->line("Tu suscripción al plan {$this->subscription->plan->nombre} vence {$when} ({$expiration->format('d/m/Y')}).")
            ->line('Renuévala a tiempo para no perder el acceso.')
            ->salutation('Saludos, '.config('app.name'));
    }
}
