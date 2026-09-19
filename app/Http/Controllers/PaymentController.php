<?php

namespace App\Http\Controllers;

use App\Exceptions\ExpiredSubscriptionException;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Temporary subscriptions until the Subscription module is integrated.
     */
    private function getMockSubscriptions(): array
    {
        return [
            1 => [
                'id' => 1,
                'customer_name' => 'Juan Pérez',
                'plan_name' => 'Plan Básico',
                'start_date' => '2026-09-01',
                'expiration_date' => '2026-12-31',
                'status' => 'ACTIVE',
            ],

            2 => [
                'id' => 2,
                'customer_name' => 'María López',
                'plan_name' => 'Plan Premium',
                'start_date' => '2026-01-01',
                'expiration_date' => '2026-08-31',
                'status' => 'EXPIRED',
            ],

            3 => [
                'id' => 3,
                'customer_name' => 'Carlos Mendoza',
                'plan_name' => 'Plan Estándar',
                'start_date' => '2026-09-10',
                'expiration_date' => '2027-03-10',
                'status' => 'ACTIVE',
            ],
        ];
    }

    /**
     * Display a listing of payments.
     */
    public function index()
    {
        $payments = Payment::latest()->get();
        $subscriptions = $this->getMockSubscriptions();

        return view('payments.index', compact('payments', 'subscriptions'));
    }

    /**
     * Show the form for creating a new payment.
     */
    public function create()
    {
        $subscriptions = $this->getMockSubscriptions();

        return view('payments.create', compact('subscriptions'));
    }

    /**
     * Store a newly created payment.
     */
    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'subscription_id' => 'required|integer',
                'amount' => 'required|numeric|min:0.01',
                'payment_method' => 'required|string|max:50',
                'payment_date' => 'required|date',
                'status' => 'required|string|max:30',
            ],
            [
                'subscription_id.required' => 'Debe seleccionar una suscripción.',
                'subscription_id.integer' => 'La suscripción seleccionada no es válida.',

                'amount.required' => 'El monto es obligatorio.',
                'amount.numeric' => 'El monto debe ser un valor numérico.',
                'amount.min' => 'El monto debe ser mayor a cero.',

                'payment_method.required' => 'Debe seleccionar un método de pago.',

                'payment_date.required' => 'La fecha de pago es obligatoria.',
                'payment_date.date' => 'La fecha de pago no es válida.',

                'status.required' => 'El estado es obligatorio.',
            ]
        );

        try {
            $subscriptions = $this->getMockSubscriptions();

            if (!isset($subscriptions[$validated['subscription_id']])) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'subscription_id' => 'La suscripción seleccionada no existe.',
                    ]);
            }

            $subscription = $subscriptions[$validated['subscription_id']];

            $expirationDate = Carbon::parse(
                $subscription['expiration_date']
            );

            if (
                $subscription['status'] === 'EXPIRED' ||
                $expirationDate->isPast()
            ) {
                throw new ExpiredSubscriptionException();
            }

            $payment = Payment::create($validated);

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
        $subscriptions = $this->getMockSubscriptions();

        $subscription =
            $subscriptions[$payment->subscription_id] ?? null;

        return view(
            'payments.show',
            compact('payment', 'subscription')
        );
    }

    /**
     * Show the form for editing the specified payment.
     */
    public function edit(Payment $payment)
    {
        $subscriptions = $this->getMockSubscriptions();

        return view(
            'payments.edit',
            compact('payment', 'subscriptions')
        );
    }

    /**
     * Update the specified payment.
     */
    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate(
            [
                'subscription_id' => 'required|integer',
                'amount' => 'required|numeric|min:0.01',
                'payment_method' => 'required|string|max:50',
                'payment_date' => 'required|date',
                'status' => 'required|string|max:30',
            ],
            [
                'subscription_id.required' => 'Debe seleccionar una suscripción.',
                'amount.required' => 'El monto es obligatorio.',
                'amount.numeric' => 'El monto debe ser un valor numérico.',
                'amount.min' => 'El monto debe ser mayor a cero.',
                'payment_method.required' => 'Debe seleccionar un método de pago.',
                'payment_date.required' => 'La fecha de pago es obligatoria.',
                'status.required' => 'El estado es obligatorio.',
            ]
        );

        try {
            $subscriptions = $this->getMockSubscriptions();

            if (!isset($subscriptions[$validated['subscription_id']])) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'subscription_id' => 'La suscripción seleccionada no existe.',
                    ]);
            }

            $subscription = $subscriptions[$validated['subscription_id']];

            $expirationDate = Carbon::parse(
                $subscription['expiration_date']
            );

            if (
                $subscription['status'] === 'EXPIRED' ||
                $expirationDate->isPast()
            ) {
                throw new ExpiredSubscriptionException();
            }

            $payment->update($validated);

            return redirect()
                ->route('payments.index')
                ->with('success', 'Pago actualizado correctamente.');

        } catch (ExpiredSubscriptionException $exception) {
            return back()
                ->withInput()
                ->with('error', $exception->getMessage());
        }
    }

    /**
     * Remove the specified payment.
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
        $subscriptions = $this->getMockSubscriptions();

        $subscription =
            $subscriptions[$payment->subscription_id] ?? null;

        return view(
            'payments.receipt',
            compact('payment', 'subscription')
        );
    }
}