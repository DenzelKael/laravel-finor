@extends('adminlte::page')

@section('title', 'Detalle de Pago #' . $payment->id)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Detalle de Pago #{{ $payment->id }}</h1>
        <div>
            <a href="{{ route('payments.receipt', $payment) }}" class="btn btn-success btn-sm mr-1">
                <i class="fas fa-receipt mr-1"></i> Ver Recibo
            </a>
            <a href="{{ route('payments.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Volver al Listado
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title">Información del Comprobante</h3>
                <div class="card-tools">
                    <x-payment-status-badge :status="$payment->status" />
                </div>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Comprobante ID:</dt>
                    <dd class="col-sm-8">#{{ $payment->id }}</dd>

                    <dt class="col-sm-4">Cliente:</dt>
                    <dd class="col-sm-8">{{ $payment->subscription->client->name ?? 'N/A' }}</dd>

                    <dt class="col-sm-4">Plan Suscrito:</dt>
                    <dd class="col-sm-8">{{ $payment->subscription->plan->nombre ?? 'N/A' }}</dd>

                    <dt class="col-sm-4">Fecha de Pago:</dt>
                    <dd class="col-sm-8">{{ $payment->payment_date->format('d/m/Y') }}</dd>

                    <dt class="col-sm-4">Método de Pago:</dt>
                    <dd class="col-sm-8">{{ $payment->payment_method->label() }}</dd>

                    <dt class="col-sm-4">Monto Pagado:</dt>
                    <dd class="col-sm-8"><span class="text-success h5 font-weight-bold">{{ $payment->formatted_amount }}</span></dd>

                    <dt class="col-sm-4">Fecha de Registro:</dt>
                    <dd class="col-sm-8">{{ $payment->created_at->format('d/m/Y H:i') }}</dd>
                </dl>
            </div>
            <div class="card-footer d-flex justify-content-end">
                <a href="{{ route('payments.receipt', $payment) }}" class="btn btn-primary mr-2">
                    <i class="fas fa-print mr-1"></i> Imprimir Recibo
                </a>
                <a href="{{ route('payments.index') }}" class="btn btn-secondary">Volver</a>
            </div>
        </div>
    </div>
</div>
@endsection