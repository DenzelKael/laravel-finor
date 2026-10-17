<?php

namespace Tests\Feature;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Client;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class PaymentReceiptPdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorized_user_can_download_payment_receipt_as_pdf(): void
    {
        $user = User::factory()->create();

        $permission = Permission::findOrCreate(
            'payments.view',
            'web'
        );

        $user->givePermissionTo($permission);

        $client = Client::create([
            'name' => 'Cliente Recibo PDF',
            'email' => 'recibo.pdf@prueba.com',
            'phone' => '70000030',
        ]);

        $plan = Plan::create([
            'nombre' => 'Plan PDF',
            'descripcion' => 'Plan para probar recibo PDF',
            'precio' => 500.00,
            'duracion_dias' => 30,
            'activo' => true,
        ]);

        $subscription = Subscription::factory()
            ->active()
            ->create([
                'client_id' => $client->id,
                'plan_id' => $plan->id,
            ]);

        $payment = $subscription
            ->payments()
            ->create([
                'amount' => 150.00,
                'payment_method' => PaymentMethod::Cash,
                'payment_date' => now()->toDateString(),
                'status' => PaymentStatus::Registered,
            ]);

        $response = $this->actingAs($user)
            ->get(
                route(
                    'payments.receipt.pdf',
                    $payment
                )
            );

        $response->assertOk();

        $response->assertHeader(
            'content-type',
            'application/pdf'
        );

        $response->assertHeader(
            'content-disposition',
            'attachment; filename=recibo-pago-' .
            $payment->id .
            '.pdf'
        );

        $this->assertStringStartsWith(
            '%PDF',
            $response->getContent()
        );
    }
}