<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use App\Models\Subscription;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use App\Http\Requests\VoidPaymentRequest;
use App\Models\Client;
use Barryvdh\DomPDF\Facade\Pdf;
class PaymentController extends Controller
{
    /**
     * Muestra el listado paginado de pagos con sus relaciones.
     */
    public function index(): View
    {
        $this->authorize('viewAny', Payment::class);

        $payments = Payment::with([
            'subscription.client',
            'subscription.plan',
        ])
            ->latest('payment_date')
            ->latest('id')
            ->paginate(15);

        return view('payments.index', compact('payments'));
    }

    /**
     * Muestra el formulario para registrar un nuevo pago.
     */
    public function create(): View
    {
        $this->authorize('create', Payment::class);

        $subscriptions = Subscription::active()
            ->with(['client', 'plan'])
            ->get();

        return view('payments.create', compact('subscriptions'));
    }

    /**
     * Registra un pago y responde en JSON para el ApiClient.
     */
    public function store(
        StorePaymentRequest $request,
        PaymentService $paymentService
    ): JsonResponse {
        $payment = $paymentService->registerPayment(
            $request->subscription(),
            $request->toDto()
        );

        return response()->json([
            'data' => new PaymentResource($payment),
            'message' => 'Pago registrado correctamente.',
        ], 201);
    }

    /**
     * Muestra el detalle del pago.
     */
    public function show(Payment $payment): View
    {
        $this->authorize('view', $payment);

        $payment->load([
            'subscription.client',
            'subscription.plan',
        ]);

        return view('payments.show', compact('payment'));
    }

    /**
     * Muestra el recibo del pago.
     */
    public function receipt(Payment $payment): View
    {
        $this->authorize('view', $payment);

        $payment->load([
            'subscription.client',
            'subscription.plan',
        ]);

        return view('payments.receipt', compact('payment'));
    }

    /**
     * Anula un pago sin eliminar el registro físico.
     */
public function cancel(
    VoidPaymentRequest $request,
    Payment $payment,
    PaymentService $paymentService
): JsonResponse {
    $paymentService->voidPayment(
        $payment,
        $request->voidReason()
    );

    return response()->json([
        'message' => 'Pago anulado correctamente.',
    ]);
}
/**
 * Muestra el historial de pagos de una suscripción.
 */
public function historyBySubscription(
    Subscription $subscription
): View {
    $this->authorize('viewAny', Payment::class);

    $subscription->load(['client', 'plan']);

    $payments = Payment::query()
        ->forSubscription($subscription->id)
        ->latest('payment_date')
        ->latest('id')
        ->paginate(15);

    return view('payments.history', [
        'payments' => $payments,
        'title' => 'Historial de Pagos por Suscripción',
        'subtitle' => sprintf(
            '%s — %s',
            $subscription->client->name,
            $subscription->plan->nombre
        ),
    ]);
}

/**
 * Muestra el historial de pagos de un cliente.
 */
public function historyByClient(
    Client $client
): View {
    $this->authorize('viewAny', Payment::class);

    $payments = Payment::query()
        ->with(['subscription.plan'])
        ->forClient($client->id)
        ->latest('payment_date')
        ->latest('id')
        ->paginate(15);

    return view('payments.history', [
        'payments' => $payments,
        'title' => 'Historial de Pagos por Cliente',
        'subtitle' => $client->name,
    ]);
}
public function receiptPdf(Payment $payment)
{
    $this->authorize('view', $payment);

    $payment->loadMissing([
        'subscription.client',
        'subscription.plan',
    ]);

    $pdf = Pdf::loadView(
        'payments.receipt-pdf',
        compact('payment')
    )->setPaper('a4');

    return $pdf->download(
        'recibo-pago-' . $payment->id . '.pdf'
    );
}
}