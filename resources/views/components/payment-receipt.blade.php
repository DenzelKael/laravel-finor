@props(['payment'])

<div class="invoice p-3 mb-3">

    <div class="row">
        <div class="col-12">
            <h4>
                <i class="fas fa-dumbbell text-primary"></i>
                Gimnasio FINOR

                <small class="float-right">
                    Fecha:
                    {{ $payment->payment_date->format('d/m/Y') }}
                </small>
            </h4>
        </div>
    </div>

    <div class="row invoice-info mt-3">
        <div class="col-sm-6 invoice-col">
            De

            <address>
                <strong>Gimnasio FINOR S.R.L.</strong><br>
                Montero, Santa Cruz - Bolivia<br>
                Teléfono: (591) 700-00000<br>
                Email: info@finorgym.com
            </address>
        </div>

        <div class="col-sm-6 invoice-col">
            Para

            <address>
                <strong>
                    {{ $payment->subscription->client->name ?? 'Cliente General' }}
                </strong><br>

                Email:
                {{ $payment->subscription->client->email ?? 'N/A' }}<br>

                Teléfono:
                {{ $payment->subscription->client->phone ?? 'N/A' }}
            </address>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-12 table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nro. Transacción</th>
                        <th>Concepto</th>
                        <th>Método</th>
                        <th>Estado</th>
                        <th class="text-right">Subtotal</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td>
                            PAY-{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}
                        </td>

                        <td>
                            Pago de Suscripción —
                            Plan {{ $payment->subscription->plan->nombre ?? 'General' }}
                        </td>

                        <td>
                            {{ $payment->payment_method->label() }}
                        </td>

                        <td>
                            <x-payment-status-badge
                                :status="$payment->status"
                            />
                        </td>

                        <td class="text-right">
                            {{ $payment->formatted_amount }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    @if ($payment->status === \App\Enums\PaymentStatus::Voided)
        <div class="alert alert-danger mt-3">
            <strong>Pago anulado.</strong>

            @if ($payment->void_reason)
                <br>
                Motivo: {{ $payment->void_reason }}
            @endif

            @if ($payment->voided_at)
                <br>
                Fecha de anulación:
                {{ $payment->voided_at->format('d/m/Y H:i') }}
            @endif
        </div>
    @endif

    <div class="row mt-3">
        <div class="col-6">
            <p class="lead">
                Método de Pago:
            </p>

            <p class="text-muted well well-sm shadow-none mt-2">
                Pago procesado mediante
                <strong>
                    {{ $payment->payment_method->label() }}
                </strong>
                el día
                {{ $payment->payment_date->format('d/m/Y') }}.
            </p>
        </div>

        <div class="col-6">
            <div class="table-responsive">
                <table class="table">
                    <tr>
                        <th style="width: 50%">
                            Total Pagado:
                        </th>

                        <td class="text-right h4 font-weight-bold text-success">
                            {{ $payment->formatted_amount }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

</div>