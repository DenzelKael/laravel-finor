<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\VoidPaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Client;
use App\Models\Payment;
use App\Models\Subscription;
use App\Services\PaymentService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
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

    public function create(): View
    {
        $this->authorize('create', Payment::class);
$subscriptions = Subscription::payable()
    ->with(['client', 'plan'])
    ->get();

        return view('payments.create', compact('subscriptions'));
    }

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

    public function show(Payment $payment): View
    {
        $this->authorize('view', $payment);

        $payment->load([
            'subscription.client',
            'subscription.plan',
        ]);

        return view('payments.show', compact('payment'));
    }

    public function receipt(Payment $payment): View
    {
        $this->authorize('view', $payment);

        $payment->load([
            'subscription.client',
            'subscription.plan',
        ]);

        return view('payments.receipt', compact('payment'));
    }

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

    public function historyBySubscription(
        Request $request,
        Subscription $subscription
    ): JsonResponse|View {
        $this->authorize('viewAny', Payment::class);

        $subscription->load(['client', 'plan']);

        if (! $request->expectsJson()) {
            return view('payments.history', [
                'title' => 'Historial de Pagos por Suscripción',
                'subtitle' => sprintf(
                    '%s — %s',
                    $subscription->client->name,
                    $subscription->plan->nombre
                ),
                'historyUrl' => route(
                    'payments.history.subscription',
                    $subscription
                ),
            ]);
        }

        $payments = Payment::query()
            ->forSubscription($subscription->id)
            ->latest('payment_date')
            ->latest('id')
            ->paginate(15);

        return response()->json([
            'data' => PaymentResource::collection(
                $payments->getCollection()
            )->resolve($request),
            'meta' => [
                'current_page' => $payments->currentPage(),
                'last_page' => $payments->lastPage(),
                'per_page' => $payments->perPage(),
                'total' => $payments->total(),
            ],
        ]);
    }

    public function historyByClient(
        Request $request,
        Client $client
    ): JsonResponse|View {
        $this->authorize('viewAny', Payment::class);

        if (! $request->expectsJson()) {
            return view('payments.history', [
                'title' => 'Historial de Pagos por Cliente',
                'subtitle' => $client->name,
                'historyUrl' => route(
                    'payments.history.client',
                    $client
                ),
            ]);
        }

        $payments = Payment::query()
            ->forClient($client->id)
            ->latest('payment_date')
            ->latest('id')
            ->paginate(15);

        return response()->json([
            'data' => PaymentResource::collection(
                $payments->getCollection()
            )->resolve($request),
            'meta' => [
                'current_page' => $payments->currentPage(),
                'last_page' => $payments->lastPage(),
                'per_page' => $payments->perPage(),
                'total' => $payments->total(),
            ],
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