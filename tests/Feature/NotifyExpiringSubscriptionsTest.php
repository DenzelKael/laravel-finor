<?php

namespace Tests\Feature;

use App\Enums\RoleName;
use App\Enums\SubscriptionStatus;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\SubscriptionExpiringNotification;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class NotifyExpiringSubscriptionsTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_notifies_only_subscriptions_expiring_within_requested_days(): void
    {
        Carbon::setTestNow('2026-11-20 10:00:00');

        Notification::fake();

        // Crear usuario administrador.
        $admin = User::factory()->create();

        $adminRole = Role::firstOrCreate([
            'name' => RoleName::Admin->value,
            'guard_name' => 'web',
        ]);

        $admin->assignRole($adminRole);

        // Suscripción que vence dentro de los 3 días.
        $expiringSubscription = Subscription::factory()
            ->expiringInDays(3)
            ->create();

        // Suscripción que vence fuera del plazo.
        Subscription::factory()
            ->expiringInDays(4)
            ->create();

        // Suscripción cancelada dentro del plazo.
        Subscription::factory()
            ->expiringInDays(2)
            ->cancelled()
            ->create();

        // Suscripción vencida que todavía figura como activa.
        Subscription::factory()
            ->state([
                'status' => SubscriptionStatus::Active->value,
                'expiration_date' => today()->subDay(),
            ])
            ->create();

        // Ejecutar el comando con un plazo de 3 días.
        $this->artisan('subscriptions:notify-expiring', [
            '--days' => 3,
        ])->assertExitCode(0);

        // Comprobar que se notificó la suscripción correspondiente.
        Notification::assertSentTo(
            $admin,
            SubscriptionExpiringNotification::class,
            fn ($notification) => $notification->subscription->is(
                $expiringSubscription
            )
        );

        // Comprobar que solamente se envió una notificación.
        Notification::assertSentToTimes(
            $admin,
            SubscriptionExpiringNotification::class,
            1
        );
    }

    public function test_does_not_send_duplicate_notification_for_same_expiration_date(): void
    {
        Carbon::setTestNow('2026-11-20 10:00:00');

        $admin = User::factory()->create();

        $adminRole = Role::firstOrCreate([
            'name' => RoleName::Admin->value,
            'guard_name' => 'web',
        ]);

        $admin->assignRole($adminRole);

        Subscription::factory()
            ->expiringInDays(3)
            ->create();

        // Primera ejecución: debe guardar la notificación.
        $this->artisan('subscriptions:notify-expiring', [
            '--days' => 3,
        ])->assertExitCode(0);

        // Segunda ejecución: no debe guardar otra notificación.
        $this->artisan('subscriptions:notify-expiring', [
            '--days' => 3,
        ])->assertExitCode(0);

        // Debe existir solamente una notificación en la base de datos.
        $this->assertSame(
            1,
            $admin->notifications()
                ->where('type', SubscriptionExpiringNotification::class)
                ->count()
        );

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $admin->id,
            'notifiable_type' => User::class,
            'type' => SubscriptionExpiringNotification::class,
        ]);
    }

    public function test_sends_new_notification_when_expiration_date_changes(): void
    {
        Carbon::setTestNow('2026-11-20 10:00:00');

        $admin = User::factory()->create();

        $adminRole = Role::firstOrCreate([
            'name' => RoleName::Admin->value,
            'guard_name' => 'web',
        ]);

        $admin->assignRole($adminRole);

        $subscription = Subscription::factory()
            ->expiringInDays(3)
            ->create();

        // Primera notificación.
        $this->artisan('subscriptions:notify-expiring', [
            '--days' => 5,
        ])->assertExitCode(0);

        // Cambiar la fecha de vencimiento de la suscripción.
        $subscription->update([
            'expiration_date' => today()->addDays(4),
        ]);

        // Debe permitir una nueva notificación.
        $this->artisan('subscriptions:notify-expiring', [
            '--days' => 5,
        ])->assertExitCode(0);

        $this->assertSame(
            2,
            $admin->notifications()
                ->where('type', SubscriptionExpiringNotification::class)
                ->count()
        );
    }
}
