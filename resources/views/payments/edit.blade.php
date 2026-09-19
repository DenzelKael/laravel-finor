@extends('adminlte::page')

@section('title', 'Editar pago')

@section('content_header')

    <div>

        <h1 class="mb-0">
            Editar pago
        </h1>

        <small class="text-muted">
            Modifique la información del pago #{{ $payment->id }}
        </small>

    </div>

@stop

@section('content')


    @if (session('error'))

        <div class="alert alert-danger">

            <i class="fas fa-exclamation-triangle mr-2"></i>

            {{ session('error') }}

        </div>

    @endif


    @if ($errors->any())

        <div class="alert alert-danger">

            <strong>
                Revise la información ingresada.
            </strong>

            <ul class="mb-0 mt-2">

                @foreach ($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    <div class="card card-warning">

        <div class="card-header">

            <h3 class="card-title">

                <i class="fas fa-edit mr-1"></i>

                Información del pago

            </h3>

        </div>


        <form
            action="{{ route('payments.update', $payment) }}"
            method="POST"
        >

            @csrf
            @method('PUT')


            <div class="card-body">


                {{-- Subscription --}}
                <div class="form-group">

                    <label for="subscription_id">
                        Suscripción
                    </label>

                    <select
                        name="subscription_id"
                        id="subscription_id"
                        class="form-control @error('subscription_id') is-invalid @enderror"
                        required
                    >

                        @foreach ($subscriptions as $subscription)

                            <option
                                value="{{ $subscription['id'] }}"
                                {{
                                    old(
                                        'subscription_id',
                                        $payment->subscription_id
                                    ) == $subscription['id']
                                        ? 'selected'
                                        : ''
                                }}
                            >

                                #{{ $subscription['id'] }}
                                -
                                {{ $subscription['customer_name'] }}
                                -
                                {{ $subscription['plan_name'] }}

                                @if ($subscription['status'] === 'EXPIRED')

                                    - VENCIDA

                                @else

                                    - ACTIVA

                                @endif

                            </option>

                        @endforeach

                    </select>

                    @error('subscription_id')

                        <span class="invalid-feedback">
                            {{ $message }}
                        </span>

                    @enderror

                </div>


                {{-- Amount --}}
                <div class="form-group">

                    <label for="amount">
                        Monto
                    </label>

                    <div class="input-group">

                        <div class="input-group-prepend">

                            <span class="input-group-text">
                                Bs.
                            </span>

                        </div>

                        <input
                            type="number"
                            name="amount"
                            id="amount"
                            class="form-control @error('amount') is-invalid @enderror"
                            value="{{ old('amount', $payment->amount) }}"
                            step="0.01"
                            min="0.01"
                            required
                        >

                        @error('amount')

                            <span class="invalid-feedback">
                                {{ $message }}
                            </span>

                        @enderror

                    </div>

                </div>


                {{-- Payment method --}}
                <div class="form-group">

                    <label for="payment_method">
                        Método de pago
                    </label>

                    @php

                        $selectedPaymentMethod = old(
                            'payment_method',
                            $payment->payment_method
                        );

                    @endphp

                    <select
                        name="payment_method"
                        id="payment_method"
                        class="form-control @error('payment_method') is-invalid @enderror"
                        required
                    >

                        <option
                            value="CASH"
                            {{ $selectedPaymentMethod === 'CASH' ? 'selected' : '' }}
                        >
                            Efectivo
                        </option>

                        <option
                            value="CARD"
                            {{ $selectedPaymentMethod === 'CARD' ? 'selected' : '' }}
                        >
                            Tarjeta
                        </option>

                        <option
                            value="TRANSFER"
                            {{ $selectedPaymentMethod === 'TRANSFER' ? 'selected' : '' }}
                        >
                            Transferencia bancaria
                        </option>

                        <option
                            value="QR"
                            {{ $selectedPaymentMethod === 'QR' ? 'selected' : '' }}
                        >
                            QR
                        </option>

                    </select>

                    @error('payment_method')

                        <span class="invalid-feedback">
                            {{ $message }}
                        </span>

                    @enderror

                </div>


                {{-- Payment date --}}
                <div class="form-group">

                    <label for="payment_date">
                        Fecha de pago
                    </label>

                    <input
                        type="date"
                        name="payment_date"
                        id="payment_date"
                        class="form-control @error('payment_date') is-invalid @enderror"
                        value="{{ old(
                            'payment_date',
                            $payment->payment_date->format('Y-m-d')
                        ) }}"
                        required
                    >

                    @error('payment_date')

                        <span class="invalid-feedback">
                            {{ $message }}
                        </span>

                    @enderror

                </div>


                {{-- Status --}}
                <div class="form-group">

                    <label for="status">
                        Estado
                    </label>

                    @php

                        $selectedStatus = old(
                            'status',
                            $payment->status
                        );

                    @endphp

                    <select
                        name="status"
                        id="status"
                        class="form-control @error('status') is-invalid @enderror"
                        required
                    >

                        <option
                            value="REGISTERED"
                            {{ $selectedStatus === 'REGISTERED' ? 'selected' : '' }}
                        >
                            Registrado
                        </option>

                        <option
                            value="PENDING"
                            {{ $selectedStatus === 'PENDING' ? 'selected' : '' }}
                        >
                            Pendiente
                        </option>

                        <option
                            value="CANCELLED"
                            {{ $selectedStatus === 'CANCELLED' ? 'selected' : '' }}
                        >
                            Anulado
                        </option>

                    </select>

                    @error('status')

                        <span class="invalid-feedback">
                            {{ $message }}
                        </span>

                    @enderror

                </div>

            </div>


            <div class="card-footer">

                <button
                    type="submit"
                    class="btn btn-warning"
                >

                    <i class="fas fa-save mr-1"></i>
                    Guardar cambios

                </button>


                <a
                    href="{{ route('payments.index') }}"
                    class="btn btn-secondary"
                >

                    <i class="fas fa-arrow-left mr-1"></i>
                    Cancelar

                </a>

            </div>

        </form>

    </div>

@stop