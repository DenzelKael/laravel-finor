<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Http\Requests\StorePaymentRequest;
use App\Models\Payment;
use App\Models\Subscription;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class PaymentController extends Controller
{
    /**
     * Muestra el listado paginado de pagos con sus relaciones.
     */
    public function index(): View
    {
        $payments = Payment::with(['subscription.client', 'subscription.plan'])
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
        $subscription = Subscription::findOrFail($request->validated('subscription_id'));

        $payment = $paymentService->registerPayment(
            $subscription,
            $request->toDto()
        );

        return response()->json([
            'data' => $payment,
            'message' => 'Pago registrado correctamente.',
            'redirect' => route('payments.receipt', $payment),
        ], 201);
    }

    /**
     * Muestra el detalle del pago.
     */
    public function show(Payment $payment): View
    {
        $payment->load(['subscription.client', 'subscription.plan']);

        return view('payments.show', compact('payment'));
    }

    /**
     * Muestra el recibo del pago.
     */
    public function receipt(Payment $payment): View
    {
        $payment->load(['subscription.client', 'subscription.plan']);

        return view('payments.receipt', compact('payment'));
    }

    /**
     * Anula un pago sin eliminar el registro físico (Trazabilidad contable).
     */
    public function cancel(Payment $payment): JsonResponse
    {
        $this->authorize('cancel', $payment);

        $payment->update([
            'status' => PaymentStatus::Cancelled,
        ]);

        return response()->json([
            'message' => 'Pago anulado correctamente.',
        ]);
    }
}