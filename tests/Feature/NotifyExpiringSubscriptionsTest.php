<?php

namespace Tests\Feature;

use App\Enums\SubscriptionStatus;
use App\Models\Client;
use App\Models\Plan;
use App\Models\Subscription;
use App\Notifications\SubscriptionExpiringNotification;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotifyExpiringSubscriptionsTest extends TestCase
{
    use RefreshDatabase;

    private Plan $plan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->plan = Plan::create([
            'nombre' => 'Plan Mensual',
            'descripcion' => 'Plan de prueba',
            'precio' => 100.00,
            'duracion_dias' => 30,
            'activo' => true,
        ]);
    }

    private function subscription(int $daysUntilExpiration, SubscriptionStatus $status = SubscriptionStatus::Active): Subscription
    {
        $client = Client::create([
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => '70000000',
            'address' => 'Av. Test',
        ]);

        return Subscription::create([
            'client_id' => $client->id,
            'plan_id' => $this->plan->id,
            'start_date' => today()->subDays(30)->toDateString(),
            'expiration_date' => today()->addDays($daysUntilExpiration)->toDateString(),
            'status' => $status,
        ]);
    }

    public function test_scope_expiring_within_returns_only_active_subscriptions_in_range(): void
    {
        $today = $this->subscription(0);
        $inThreeDays = $this->subscription(3);
        $this->subscription(4);
        $this->subscription(-1);
        $this->subscription(2, SubscriptionStatus::Cancelled);

        $ids = Subscription::expiringWithin(3)->pluck('id')->all();

        $this->assertEqualsCanonicalizing([$today->id, $inThreeDays->id], $ids);
    }

    public function test_command_notifies_only_subscriptions_expiring_within_default_days(): void
    {
        Notification::fake();

        $expiring = $this->subscription(2);
        $later = $this->subscription(10);
        $past = $this->subscription(-2);
        $cancelled = $this->subscription(1, SubscriptionStatus::Cancelled);
        $expired = $this->subscription(1, SubscriptionStatus::Expired);

        $this->artisan('subscriptions:notify-expiring')
            ->expectsOutputToContain('Se notificaron 1 suscripciones')
            ->assertExitCode(0);

        Notification::assertSentTo(
            $expiring->client,
            SubscriptionExpiringNotification::class,
            fn (SubscriptionExpiringNotification $n, array $channels) => $n->subscription->is($expiring) && $channels === ['mail']
        );

        foreach ([$later, $past, $cancelled, $expired] as $subscription) {
            Notification::assertNotSentTo($subscription->client, SubscriptionExpiringNotification::class);
        }

        Notification::assertSentTimes(SubscriptionExpiringNotification::class, 1);
    }

    public function test_command_respects_days_option(): void
    {
        Notification::fake();

        $inSevenDays = $this->subscription(7);
        $inEightDays = $this->subscription(8);

        $this->artisan('subscriptions:notify-expiring', ['--days' => 7])
            ->assertExitCode(0);

        Notification::assertSentTo($inSevenDays->client, SubscriptionExpiringNotification::class);
        Notification::assertNotSentTo($inEightDays->client, SubscriptionExpiringNotification::class);
    }

    public function test_command_rejects_invalid_days_option(): void
    {
        Notification::fake();

        $this->artisan('subscriptions:notify-expiring', ['--days' => 'abc'])
            ->assertExitCode(2);

        $this->artisan('subscriptions:notify-expiring', ['--days' => -1])
            ->assertExitCode(2);

        Notification::assertNothingSent();
    }

    public function test_mail_message_includes_plan_and_expiration_date(): void
    {
        $subscription = $this->subscription(3);

        $mail = (new SubscriptionExpiringNotification($subscription))->toMail($subscription->client);

        $this->assertInstanceOf(MailMessage::class, $mail);
        $this->assertSame('Tu suscripción está por vencer', $mail->subject);
        $this->assertStringContainsString('Plan Mensual', $mail->introLines[0]);
        $this->assertStringContainsString('en 3 días', $mail->introLines[0]);
        $this->assertStringContainsString(today()->addDays(3)->format('d/m/Y'), $mail->introLines[0]);
    }

    public function test_command_is_scheduled_daily_at_eight(): void
    {
        $event = collect(app(Schedule::class)->events())
            ->first(fn ($event) => str_contains($event->command, 'subscriptions:notify-expiring'));

        $this->assertNotNull($event);
        $this->assertStringContainsString('--days=3', $event->command);
        $this->assertSame('0 8 * * *', $event->expression);
    }
}
