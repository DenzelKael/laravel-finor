@extends('adminlte::page')

@section('title', $title)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1>{{ $title }}</h1>
            <p class="text-muted mb-0">{{ $subtitle }}</p>
        </div>

        <a href="{{ route('payments.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left mr-1"></i>
            Volver
        </a>
    </div>
@endsection

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Método</th>
                        <th>Monto</th>
                        <th>Estado</th>
                        <th>Motivo de Anulación</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($payments as $payment)
                        <tr>
                            <td>#{{ $payment->id }}</td>

                            <td>
                                {{ $payment->payment_date->format('d/m/Y') }}
                            </td>

                            <td>
                                {{ $payment->payment_method->label() }}
                            </td>

                            <td>
                                {{ $payment->formatted_amount }}
                            </td>

                            <td>
                                <x-payment-status-badge
                                    :status="$payment->status"
                                />
                            </td>

                            <td>
                                {{ $payment->void_reason ?? '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td
                                colspan="6"
                                class="text-center text-muted py-4"
                            >
                                No existen pagos registrados.
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