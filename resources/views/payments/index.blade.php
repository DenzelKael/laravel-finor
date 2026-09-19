@extends('adminlte::page')

@section('title', 'Pagos')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-0">Gestión de pagos</h1>
            <small class="text-muted">
                Registro y administración de pagos asociados a suscripciones
            </small>
        </div>

        <a href="{{ route('payments.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Registrar pago
        </a>
    </div>
@stop

@section('content')

    {{-- Success message --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}

            <button type="button"
                    class="close"
                    data-dismiss="alert"
                    aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Error message --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            {{ session('error') }}

            <button type="button"
                    class="close"
                    data-dismiss="alert"
                    aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card">

        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-money-bill-wave mr-1"></i>
                Pagos registrados
            </h3>
        </div>

        <div class="card-body table-responsive p-0">

            <table class="table table-hover table-striped mb-0">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Suscripción</th>
                        <th>Cliente</th>
                        <th>Monto</th>
                        <th>Método de pago</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($payments as $payment)

                        @php
                            $subscription = $subscriptions[$payment->subscription_id] ?? null;

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

                            $statusLabel = $statusLabels[$payment->status] ?? $payment->status;
                            $statusClass = $statusClasses[$payment->status] ?? 'secondary';
                        @endphp

                        <tr>

                            <td>
                                {{ $payment->id }}
                            </td>

                            <td>
                                #{{ $payment->subscription_id }}

                                @if ($subscription)
                                    <br>
                                    <small class="text-muted">
                                        {{ $subscription['plan_name'] }}
                                    </small>
                                @endif
                            </td>

                            <td>
                                {{ $subscription['customer_name'] ?? 'No disponible' }}
                            </td>

                            <td>
                                <strong>
                                    Bs. {{ number_format((float) $payment->amount, 2, ',', '.') }}
                                </strong>
                            </td>

                            <td>
                                {{ $paymentMethodLabels[$payment->payment_method]
                                    ?? $payment->payment_method }}
                            </td>

                            <td>
                                {{ $payment->payment_date->format('d/m/Y') }}
                            </td>

                            <td>
                                <span class="badge badge-{{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>

                            <td class="text-center">

                                <a href="{{ route('payments.show', $payment) }}"
                                   class="btn btn-info btn-sm"
                                   title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <a href="{{ route('payments.receipt', $payment) }}"
                                   class="btn btn-secondary btn-sm"
                                   title="Recibo">
                                    <i class="fas fa-receipt"></i>
                                </a>

                                <a href="{{ route('payments.edit', $payment) }}"
                                   class="btn btn-warning btn-sm"
                                   title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <form action="{{ route('payments.destroy', $payment) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('¿Está seguro de eliminar este pago?');">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-danger btn-sm"
                                            title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>

                                </form>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="8"
                                class="text-center text-muted py-4">

                                <i class="fas fa-info-circle mr-1"></i>
                                No existen pagos registrados.

                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

@stop