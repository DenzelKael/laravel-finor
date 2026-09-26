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

        <a
            href="{{ route('payments.receipt', $payment) }}"
            class="btn btn-secondary"
        >
            <i class="fas fa-receipt mr-1"></i>
            Ver recibo
        </a>

    </div>

@stop

@section('content')

    @include('payments.partials.alerts')

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
                                Bs.
                                {{ number_format((float) $payment->amount, 2, ',', '.') }}
                            </strong>
                        </dd>

                        <dt class="col-sm-5">
                            Método de pago
                        </dt>

                        <dd class="col-sm-7">
                            {{ $payment->payment_method_label }}
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
                            <span class="badge badge-{{ $payment->status_badge }}">
                                {{ $payment->status_label }}
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

                    @if ($payment->subscription)

                        <dl class="row">

                            <dt class="col-sm-5">
                                Suscripción
                            </dt>

                            <dd class="col-sm-7">
                                #{{ $payment->subscription->id }}
                            </dd>

                            <dt class="col-sm-5">
                                Cliente
                            </dt>

                            <dd class="col-sm-7">
                                {{ $payment->subscription->customer_name }}
                            </dd>

                            <dt class="col-sm-5">
                                Plan
                            </dt>

                            <dd class="col-sm-7">
                                {{ $payment->subscription->plan_name }}
                            </dd>

                            <dt class="col-sm-5">
                                Fecha de inicio
                            </dt>

                            <dd class="col-sm-7">
                                {{ $payment->subscription->start_date->format('d/m/Y') }}
                            </dd>

                            <dt class="col-sm-5">
                                Fecha de vencimiento
                            </dt>

                            <dd class="col-sm-7">
                                {{ $payment->subscription->expiration_date->format('d/m/Y') }}
                            </dd>

                            <dt class="col-sm-5">
                                Estado
                            </dt>

                            <dd class="col-sm-7">

                                @if ($payment->subscription->isExpired())

                                    <span class="badge badge-danger">
                                        Vencida
                                    </span>

                                @else

                                    <span class="badge badge-success">
                                        Activa
                                    </span>

                                @endif

                            </dd>

                        </dl>

                    @else

                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            No se encontró información de la suscripción.
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
            href="{{ route('payments.receipt', $payment) }}"
            class="btn btn-info"
        >
            <i class="fas fa-receipt mr-1"></i>
            Ver recibo
        </a>

    </div>

@stop