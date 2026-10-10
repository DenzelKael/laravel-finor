@extends('adminlte::page')

@section('title', 'Suscripciones')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Suscripciones</h1>

        @can('create', App\Models\Subscription::class)
            <a href="{{ route('subscriptions.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nueva Suscripción
            </a>
        @endcan
    </div>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        @if ($subscriptions->isEmpty())
            <div class="alert alert-info">
                No hay suscripciones registradas.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Plan</th>
                            <th>Inicio</th>
                            <th>Vencimiento</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($subscriptions as $subscription)
                            <tr>
                                <td>{{ $subscription->id }}</td>
                                <td>{{ $subscription->client?->name ?? 'Sin cliente' }}</td>
                                <td>{{ $subscription->plan?->nombre ?? 'Sin plan' }}</td>
                                <td>{{ $subscription->start_date?->format('d/m/Y') }}</td>
                                <td>{{ $subscription->expiration_date?->format('d/m/Y') }}</td>
                                <td>
                                    @php
                                        $statusClass = match ($subscription->status->value) {
                                            'ACTIVE' => 'success',
                                            'EXPIRED' => 'warning',
                                            'CANCELLED' => 'danger',
                                            default => 'secondary',
                                        };
                                    @endphp

                                    <span class="badge badge-{{ $statusClass }}">
                                        {{ $subscription->status->label() }}
                                    </span>
                                </td>
                                <td>
                                    @can('renew', $subscription)
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-success btn-renew-subscription"
                                            data-renew-url="{{ route('subscriptions.renew', $subscription) }}">
                                            <i class="fas fa-sync-alt"></i> Renovar
                                        </button>
                                    @endcan

                                    @can('cancel', $subscription)
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-danger btn-cancel-subscription"
                                            data-cancel-url="{{ route('subscriptions.cancel', $subscription) }}">
                                            <i class="fas fa-ban"></i> Cancelar
                                        </button>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $subscriptions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('js')
    @vite('resources/js/pages/subscriptions-index.js')
@endpush