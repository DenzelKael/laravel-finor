@extends('adminlte::page')

@section('title', 'Historial de Pagos')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Módulo de Pagos</h1>
        <a href="{{ route('payments.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus-circle mr-1"></i> Registrar Pago
        </a>
    </div>
@endsection

@section('content')
<div id="alert-container"></div>

<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Listado General de Pagos</h3>
    </div>

    <div class="card-body p-0 table-responsive">
        <table class="table table-hover table-striped">
            <thead>
                <tr>
                    <th style="width: 80px">ID</th>
                    <th>Cliente</th>
                    <th>Plan</th>
                    <th>Fecha de Pago</th>
                    <th>Método</th>
                    <th>Monto</th>
                    <th>Estado</th>
                    <th class="text-right" style="width: 180px">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($payments as $payment)
                    <tr id="payment-row-{{ $payment->id }}">
                        <td><strong>#{{ $payment->id }}</strong></td>
                        <td>{{ $payment->subscription->client->name ?? 'N/A' }}</td>
                        <td>{{ $payment->subscription->plan->nombre ?? 'N/A' }}</td>
                        <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                        <td>{{ $payment->payment_method->label() }}</td>
                        <td><strong>{{ $payment->formatted_amount }}</strong></td>
                        <td>
                            <x-payment-status-badge id="payment-status-{{ $payment->id }}" :status="$payment->status" />
                        </td>
                        <td class="text-right">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('payments.show', $payment) }}" class="btn btn-info" title="Ver Detalle">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('payments.receipt', $payment) }}" class="btn btn-secondary" title="Ver Recibo">
                                    <i class="fas fa-receipt"></i>
                                </a>
                                @if ($payment->status === \App\Enums\PaymentStatus::Registered)
                                    <button type="button"
                                            id="payment-action-{{ $payment->id }}"
                                            class="btn btn-danger"
                                            title="Anular Pago"
                                            onclick="confirmCancelPayment({{ $payment->id }}, '{{ route('payments.cancel', $payment) }}')">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="fas fa-info-circle mr-1"></i> No existen registros de pagos.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($payments->hasPages())
        <div class="card-footer clearfix">
            <div class="float-right">
                {{ $payments->links() }}
            </div>
        </div>
    @endif
</div>
@endsection

@section('js')
    @vite(['resources/js/payments/index.js'])
@endsection