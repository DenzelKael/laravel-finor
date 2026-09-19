@extends('adminlte::page')

@section('title', 'Registrar pago')

@section('content_header')

    <div>
        <h1 class="mb-0">Registrar pago</h1>

        <small class="text-muted">
            Registre un pago asociado a una suscripción
        </small>
    </div>

@stop

@section('content')

    {{-- Exception message --}}
    @if (session('error'))

        <div class="alert alert-danger">

            <i class="fas fa-exclamation-triangle mr-2"></i>

            {{ session('error') }}

        </div>

    @endif


    {{-- Validation errors --}}
    @if ($errors->any())

        <div class="alert alert-danger">

            <strong>
                <i class="fas fa-exclamation-circle"></i>
                Revise la información ingresada.
            </strong>

            <ul class="mb-0 mt-2">

                @foreach ($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    <div class="card card-primary">

        <div class="card-header">

            <h3 class="card-title">

                <i class="fas fa-money-bill-wave mr-1"></i>

                Información del pago

            </h3>

        </div>


        <form action="{{ route('payments.store') }}"
              method="POST">

            @csrf


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

                        <option value="">
                            Seleccione una suscripción
                        </option>

                        @foreach ($subscriptions as $subscription)

                            <option
                                value="{{ $subscription['id'] }}"
                                {{ old('subscription_id') == $subscription['id'] ? 'selected' : '' }}
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

                    <small class="form-text text-muted">
                        El sistema verificará automáticamente si la suscripción se encuentra vigente.
                    </small>

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
                            value="{{ old('amount') }}"
                            step="0.01"
                            min="0.01"
                            placeholder="0.00"
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

                    <select
                        name="payment_method"
                        id="payment_method"
                        class="form-control @error('payment_method') is-invalid @enderror"
                        required
                    >

                        <option value="">
                            Seleccione un método
                        </option>

                        <option
                            value="CASH"
                            {{ old('payment_method') === 'CASH' ? 'selected' : '' }}
                        >
                            Efectivo
                        </option>

                        <option
                            value="CARD"
                            {{ old('payment_method') === 'CARD' ? 'selected' : '' }}
                        >
                            Tarjeta
                        </option>

                        <option
                            value="TRANSFER"
                            {{ old('payment_method') === 'TRANSFER' ? 'selected' : '' }}
                        >
                            Transferencia bancaria
                        </option>

                        <option
                            value="QR"
                            {{ old('payment_method') === 'QR' ? 'selected' : '' }}
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
                        value="{{ old('payment_date', date('Y-m-d')) }}"
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

                    <select
                        name="status"
                        id="status"
                        class="form-control @error('status') is-invalid @enderror"
                        required
                    >

                        <option
                            value="REGISTERED"
                            {{ old('status', 'REGISTERED') === 'REGISTERED' ? 'selected' : '' }}
                        >
                            Registrado
                        </option>

                        <option
                            value="PENDING"
                            {{ old('status') === 'PENDING' ? 'selected' : '' }}
                        >
                            Pendiente
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
                    class="btn btn-primary"
                >

                    <i class="fas fa-save mr-1"></i>
                    Registrar pago

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