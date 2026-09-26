@extends('adminlte::page')

@section('title', 'Pagos')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-0">Gestión de pagos</h1>

            <small class="text-muted">
                Registro y consulta de pagos asociados a suscripciones
            </small>
        </div>

        <a
            href="{{ route('payments.create') }}"
            class="btn btn-primary"
        >
            <i class="fas fa-plus mr-1"></i>
            Registrar pago
        </a>
    </div>
@stop

@section('content')

    @include('payments.partials.alerts')

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
                        <th>Cliente</th>
                        <th>Plan</th>
                        <th>Monto</th>
                        <th>Método</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th class="text-center">
                            Acciones
                        </th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($payments as $payment)

                        <tr>

                            <td>
                                #{{ $payment->id }}
                            </td>

                            <td>
                                {{ $payment->subscription?->customer_name ?? 'No disponible' }}
                            </td>

                            <td>
                                {{ $payment->subscription?->plan_name ?? 'No disponible' }}
                            </td>

                            <td>
                                <strong>
                                    Bs.
                                    {{ number_format((float) $payment->amount, 2, ',', '.') }}
                                </strong>
                            </td>

                            <td>
                                {{ $payment->payment_method_label }}
                            </td>

                            <td>
                                {{ $payment->payment_date->format('d/m/Y') }}
                            </td>

                            <td>
                                <span class="badge badge-{{ $payment->status_badge }}">
                                    {{ $payment->status_label }}
                                </span>
                            </td>

                            <td class="text-center">

                                <a
                                    href="{{ route('payments.show', $payment) }}"
                                    class="btn btn-info btn-sm"
                                    title="Ver detalle"
                                >
                                    <i class="fas fa-eye"></i>
                                </a>

                                <a
                                    href="{{ route('payments.receipt', $payment) }}"
                                    class="btn btn-secondary btn-sm"
                                    title="Ver recibo"
                                >
                                    <i class="fas fa-receipt"></i>
                                </a>

                                <form
                                    action="{{ route('payments.destroy', $payment) }}"
                                    method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('¿Está seguro de eliminar este pago?');"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn btn-danger btn-sm"
                                        title="Eliminar pago"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td
                                colspan="8"
                                class="text-center text-muted py-4"
                            >
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