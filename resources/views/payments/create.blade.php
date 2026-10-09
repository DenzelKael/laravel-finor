@extends('adminlte::page')

@section('title', 'Registrar Pago')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Registrar Pago</h1>
        <a href="{{ route('payments.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left mr-1"></i> Volver al Listado
        </a>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">Formulario de Registro de Pago</h3>
            </div>

            <form id="create-payment-form"
                  data-store-url="{{ route('payments.store') }}"
                  data-index-url="{{ route('payments.index') }}">
                @csrf

                <div class="card-body">
                    <div class="form-group">
                        <label for="subscription_id">Suscripción Activa <span class="text-danger">*</span></label>
                        <select name="subscription_id" id="subscription_id" class="form-control select2" required>
                            <option value="">-- Seleccione una suscripción activa --</option>
                            @foreach ($subscriptions as $subscription)
                                <option value="{{ $subscription->id }}" @selected(old('subscription_id') == $subscription->id)>
                                    Cliente: {{ $subscription->client->name }} | Plan: {{ $subscription->plan->nombre }} (Vence: {{ $subscription->expiration_date->format('d/m/Y') }})
                                </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="amount">Monto (Bs) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Bs</span>
                                    </div>
                                    <input type="number" step="0.01" min="0.01" max="99999999.99" name="amount" id="amount" class="form-control" placeholder="0.00" value="{{ old('amount') }}" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="payment_date">Fecha de Pago <span class="text-danger">*</span></label>
                                <input type="date" name="payment_date" id="payment_date" class="form-control" max="{{ date('Y-m-d') }}" value="{{ old('payment_date', date('Y-m-d')) }}" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="payment_method">Método de Pago <span class="text-danger">*</span></label>
                        <select name="payment_method" id="payment_method" class="form-control" required>
                            <option value="">-- Seleccione un método de pago --</option>
                            @foreach (\App\Enums\PaymentMethod::cases() as $method)
                                <option value="{{ $method->value }}" @selected(old('payment_method') === $method->value)>
                                    {{ $method->label() }}
                                </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-end">
                    <a href="{{ route('payments.index') }}" class="btn btn-secondary mr-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Guardar Pago
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
    @vite(['resources/js/payments/create.js'])
@endsection