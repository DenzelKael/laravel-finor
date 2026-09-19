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

    @php

        $paymentMethodLabels = [
            'CASH' => 'Efectivo',
            'CARD' => 'Tarjeta',
            'TRANSFER' => 'Transferencia bancaria',
            'QR' => 'QR',
        ];

        $statusLabels = [
            'REGISTERED' => 'Registrado',
            'PENDING' => 'Pendiente',
            'CANCELLED' => 'Anulado',
        ];

    @endphp


    @if (session('success'))

        <div class="alert alert-success d-print-none">

            <i class="fas fa-check-circle mr-2"></i>

            {{ session('success') }}

        </div>

    @endif


    <div class="invoice p-3 mb-3">


        {{-- Header --}}
        <div class="row">

            <div class="col-12">

                <h4>

                    <i class="fas fa-receipt"></i>

                    Recibo de pago

                    <small class="float-right">

                        Fecha:
                        {{ $payment->payment_date->format('d/m/Y') }}

                    </small>

                </h4>

            </div>

        </div>


        <hr>


        {{-- General information --}}
        <div class="row invoice-info">


            <div class="col-sm-4 invoice-col">

                <strong>
                    Recibo
                </strong>

                <address>

                    N.º:
                    <strong>
                        {{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}
                    </strong>

                    <br>

                    Fecha:
                    {{ $payment->payment_date->format('d/m/Y') }}

                    <br>

                    Estado:
                    {{
                        $statusLabels[$payment->status]
                        ?? $payment->status
                    }}

                </address>

            </div>


            <div class="col-sm-4 invoice-col">

                <strong>
                    Cliente
                </strong>

                <address>

                    @if ($subscription)

                        {{ $subscription['customer_name'] }}

                        <br>

                        Suscripción:
                        #{{ $subscription['id'] }}

                        <br>

                        Plan:
                        {{ $subscription['plan_name'] }}

                    @else

                        Información no disponible

                    @endif

                </address>

            </div>


            <div class="col-sm-4 invoice-col">

                <strong>
                    Información del pago
                </strong>

                <address>

                    Método:

                    {{
                        $paymentMethodLabels[
                            $payment->payment_method
                        ]
                        ?? $payment->payment_method
                    }}

                    <br>

                    ID del pago:
                    #{{ $payment->id }}

                </address>

            </div>


        </div>


        {{-- Payment table --}}
        <div class="row">

            <div class="col-12 table-responsive">

                <table class="table table-striped">

                    <thead>

                        <tr>

                            <th>
                                Concepto
                            </th>

                            <th>
                                Suscripción
                            </th>

                            <th>
                                Método
                            </th>

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

                                @if ($subscription)

                                    {{ $subscription['plan_name'] }}

                                @else

                                    #{{ $payment->subscription_id }}

                                @endif

                            </td>

                            <td>

                                {{
                                    $paymentMethodLabels[
                                        $payment->payment_method
                                    ]
                                    ?? $payment->payment_method
                                }}

                            </td>

                            <td class="text-right">

                                Bs.
                                {{ number_format(
                                    (float) $payment->amount,
                                    2,
                                    ',',
                                    '.'
                                ) }}

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
                                    {{ number_format(
                                        (float) $payment->amount,
                                        2,
                                        ',',
                                        '.'
                                    ) }}

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
                    href="{{ route('payments.index') }}"
                    class="btn btn-secondary"
                >

                    <i class="fas fa-list mr-1"></i>

                    Ver pagos

                </a>


                <a
                    href="{{ route('payments.show', $payment) }}"
                    class="btn btn-info"
                >

                    <i class="fas fa-eye mr-1"></i>

                    Ver detalle

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
                box-shadow: none !important;
                border: none !important;
            }

        }

    </style>

@stop