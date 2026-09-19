@extends('adminlte::page')

@section('title', 'Detalle del pago')

@section('content_header')

    <div class="d-flex justify-content-between align-items-center">

        <div>

            <h1 class="mb-0">
                Detalle del pago
            </h1>

            <small class="text-muted">
                Información del pago #{{ $payment->id }}
            </small>

        </div>

        <div>

            <a
                href="{{ route('payments.receipt', $payment) }}"
                class="btn btn-secondary"
            >

                <i class="fas fa-receipt mr-1"></i>

                Ver recibo

            </a>

        </div>

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

        $statusClasses = [
            'REGISTERED' => 'success',
            'PENDING' => 'warning',
            'CANCELLED' => 'danger',
        ];

        $statusLabel =
            $statusLabels[$payment->status]
            ?? $payment->status;

        $statusClass =
            $statusClasses[$payment->status]
            ?? 'secondary';

    @endphp


    <div class="row">


        {{-- Payment information --}}
        <div class="col-md-6">

            <div class="card card-primary">

                <div class="card-header">

                    <h3 class="card-title">

                        <i class="fas fa-money-bill-wave mr-1"></i>

                        Información del pago

                    </h3>

                </div>

                <div class="card-body">

                    <dl class="row">

                        <dt class="col-sm-5">
                            Número de pago
                        </dt>

                        <dd class="col-sm-7">
                            #{{ $payment->id }}
                        </dd>


                        <dt class="col-sm-5">
                            Monto
                        </dt>

                        <dd class="col-sm-7">

                            <strong>
                                Bs. {{ number_format((float) $payment->amount, 2, ',', '.') }}
                            </strong>

                        </dd>


                        <dt class="col-sm-5">
                            Método de pago
                        </dt>

                        <dd class="col-sm-7">

                            {{
                                $paymentMethodLabels[
                                    $payment->payment_method
                                ]
                                ?? $payment->payment_method
                            }}

                        </dd>


                        <dt class="col-sm-5">
                            Fecha de pago
                        </dt>

                        <dd class="col-sm-7">

                            {{ $payment->payment_date->format('d/m/Y') }}

                        </dd>


                        <dt class="col-sm-5">
                            Estado
                        </dt>

                        <dd class="col-sm-7">

                            <span class="badge badge-{{ $statusClass }}">

                                {{ $statusLabel }}

                            </span>

                        </dd>

                    </dl>

                </div>

            </div>

        </div>


        {{-- Subscription information --}}
        <div class="col-md-6">

            <div class="card card-info">

                <div class="card-header">

                    <h3 class="card-title">

                        <i class="fas fa-file-contract mr-1"></i>

                        Información de la suscripción

                    </h3>

                </div>

                <div class="card-body">

                    @if ($subscription)

                        <dl class="row">

                            <dt class="col-sm-5">
                                Suscripción
                            </dt>

                            <dd class="col-sm-7">
                                #{{ $subscription['id'] }}
                            </dd>


                            <dt class="col-sm-5">
                                Cliente
                            </dt>

                            <dd class="col-sm-7">
                                {{ $subscription['customer_name'] }}
                            </dd>


                            <dt class="col-sm-5">
                                Plan
                            </dt>

                            <dd class="col-sm-7">
                                {{ $subscription['plan_name'] }}
                            </dd>


                            <dt class="col-sm-5">
                                Fecha de inicio
                            </dt>

                            <dd class="col-sm-7">

                                {{
                                    \Carbon\Carbon::parse(
                                        $subscription['start_date']
                                    )->format('d/m/Y')
                                }}

                            </dd>


                            <dt class="col-sm-5">
                                Fecha de vencimiento
                            </dt>

                            <dd class="col-sm-7">

                                {{
                                    \Carbon\Carbon::parse(
                                        $subscription['expiration_date']
                                    )->format('d/m/Y')
                                }}

                            </dd>


                            <dt class="col-sm-5">
                                Estado
                            </dt>

                            <dd class="col-sm-7">

                                @if ($subscription['status'] === 'ACTIVE')

                                    <span class="badge badge-success">
                                        Activa
                                    </span>

                                @else

                                    <span class="badge badge-danger">
                                        Vencida
                                    </span>

                                @endif

                            </dd>

                        </dl>

                    @else

                        <div class="alert alert-warning mb-0">

                            <i class="fas fa-exclamation-triangle mr-1"></i>

                            No fue posible obtener la información de la suscripción.

                        </div>

                    @endif

                </div>

            </div>

        </div>

    </div>


    <div class="mb-3">

        <a
            href="{{ route('payments.index') }}"
            class="btn btn-secondary"
        >

            <i class="fas fa-arrow-left mr-1"></i>

            Volver

        </a>


        <a
            href="{{ route('payments.edit', $payment) }}"
            class="btn btn-warning"
        >

            <i class="fas fa-edit mr-1"></i>

            Editar

        </a>

    </div>

@stop