<?php

namespace App\Http\Controllers;

use App\Exceptions\ExpiredSubscriptionException;
use App\Http\Requests\StorePaymentRequest;
use App\Models\Payment;
use App\Models\Subscription;
use App\Services\PaymentService;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments.
     */
    public function index()
    {
        $payments = Payment::with('subscription')->latest()->get();
        $subscriptions = Subscription::all()->keyBy('id');

        return view('payments.index', compact('payments', 'subscriptions'));
    }

    /**
     * Show the form for creating a new payment.
     */
    public function create()
    {
        $subscriptions = Subscription::all();

        return view('payments.create', compact('subscriptions'));
    }

    /**
     * Store a newly created payment in storage.
     */
    public function store(StorePaymentRequest $request, PaymentService $paymentService)
    {
        try {
            $payment = $paymentService->registerPayment($request->validated());

            return redirect()
                ->route('payments.receipt', $payment)
                ->with('success', 'Pago registrado correctamente.');

        } catch (ExpiredSubscriptionException $exception) {
            return back()
                ->withInput()
                ->with('error', $exception->getMessage());
        }
    }

    /**
     * Display the specified payment.
     */
    public function show(Payment $payment)
    {
        $payment->load('subscription');
        $subscription = $payment->subscription;

        return view('payments.show', compact('payment', 'subscription'));
    }

    /**
     * Remove the specified payment from storage.
     */
    public function destroy(Payment $payment)
    {
        $payment->delete();

        return redirect()
            ->route('payments.index')
            ->with('success', 'Pago eliminado correctamente.');
    }

    /**
     * Display the payment receipt.
     */
    public function receipt(Payment $payment)
    {
        $payment->load('subscription');
        $subscription = $payment->subscription;

        return view('payments.receipt', compact('payment', 'subscription'));
    }
}