@extends('adminlte::page')

@section('title', 'Recibo de pago')

@section('content_header')

    <div class="d-print-none">

        <h1 class="mb-0">
            Recibo de pago
        </h1>

        <small class="text-muted">
            Comprobante del pago registrado
        </small>

    </div>

@stop

@section('content')

    <div class="d-print-none">
        @include('payments.partials.alerts')
    </div>

    <div class="invoice p-3 mb-3">

        {{-- Header --}}
        <div class="row">

            <div class="col-12">

                <h4>

                    <i class="fas fa-receipt mr-1"></i>
                    Recibo de pago

                    <small class="float-right">
                        Fecha:
                        {{ $payment->payment_date->format('d/m/Y') }}
                    </small>

                </h4>

            </div>

        </div>

        <hr>

        {{-- Receipt information --}}
        <div class="row invoice-info">

            <div class="col-sm-4 invoice-col">

                <strong>
                    Recibo
                </strong>

                <address>

                    N.º
                    <strong>
                        {{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}
                    </strong>

                    <br>

                    Fecha:
                    {{ $payment->payment_date->format('d/m/Y') }}

                    <br>

                    Estado:
                    {{ $payment->status_label }}

                </address>

            </div>

            <div class="col-sm-4 invoice-col">

                <strong>
                    Cliente
                </strong>

                <address>

                    {{ $payment->subscription?->customer_name ?? 'No disponible' }}

                    <br>

                    Suscripción:
                    #{{ $payment->subscription_id }}

                    <br>

                    Plan:
                    {{ $payment->subscription?->plan_name ?? 'No disponible' }}

                </address>

            </div>

            <div class="col-sm-4 invoice-col">

                <strong>
                    Información del pago
                </strong>

                <address>

                    Método:
                    {{ $payment->payment_method_label }}

                    <br>

                    ID del pago:
                    #{{ $payment->id }}

                </address>

            </div>

        </div>

        {{-- Payment detail --}}
        <div class="row">

            <div class="col-12 table-responsive">

                <table class="table table-striped">

                    <thead>

                        <tr>
                            <th>Concepto</th>
                            <th>Plan</th>
                            <th>Método</th>
                            <th class="text-right">
                                Monto
                            </th>
                        </tr>

                    </thead>

                    <tbody>

                        <tr>

                            <td>
                                Pago de suscripción
                            </td>

                            <td>
                                {{ $payment->subscription?->plan_name ?? 'No disponible' }}
                            </td>

                            <td>
                                {{ $payment->payment_method_label }}
                            </td>

                            <td class="text-right">

                                Bs.
                                {{ number_format((float) $payment->amount, 2, ',', '.') }}

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

        {{-- Total --}}
        <div class="row">

            <div class="col-6">
            </div>

            <div class="col-6">

                <div class="table-responsive">

                    <table class="table">

                        <tr>

                            <th style="width:50%">
                                Total:
                            </th>

                            <td class="text-right">

                                <strong>
                                    Bs.
                                    {{ number_format((float) $payment->amount, 2, ',', '.') }}
                                </strong>

                            </td>

                        </tr>

                    </table>

                </div>

            </div>

        </div>

        {{-- Buttons --}}
        <div class="row d-print-none">

            <div class="col-12">

                <button
                    type="button"
                    class="btn btn-primary"
                    onclick="window.print()"
                >
                    <i class="fas fa-print mr-1"></i>
                    Imprimir recibo
                </button>

                <a
                    href="{{ route('payments.show', $payment) }}"
                    class="btn btn-info"
                >
                    <i class="fas fa-eye mr-1"></i>
                    Ver detalle
                </a>

                <a
                    href="{{ route('payments.index') }}"
                    class="btn btn-secondary"
                >
                    <i class="fas fa-list mr-1"></i>
                    Volver a pagos
                </a>

            </div>

        </div>

    </div>

@stop

@section('css')

    <style>

        @media print {

            .main-header,
            .main-sidebar,
            .content-header,
            .main-footer,
            .d-print-none {
                display: none !important;
            }

            .content-wrapper {
                margin-left: 0 !important;
            }

            .invoice {
                border: none !important;
                box-shadow: none !important;
            }

        }

    </style>

@stop