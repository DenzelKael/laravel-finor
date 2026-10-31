@extends('adminlte::page')

@section('title', 'Reporte: Suscripciones por Vencer')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1 class="m-0 font-weight-bold">Suscripciones por Vencer</h1>
            <p class="text-muted mb-0">Monitoreo de suscripciones activas próximas a expirar en 7, 15 y 30 días.</p>
        </div>
        <div>
            <a href="{{ route('subscriptions.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Ver todas las suscripciones
            </a>
        </div>
    </div>
@stop

@section('content')
    <!-- Tarjetas de resumen rápido -->
    <div class="row">
        <div class="col-lg-4 col-md-6 col-12 mb-3">
            <div class="small-box bg-danger shadow-sm">
                <div class="inner">
                    <h3>{{ $counts[7] ?? 0 }}</h3>
                    <p class="mb-0">Vencen en 7 días o menos</p>
                    <small>Atención prioritaria y cobranza urgente</small>
                </div>
                <div class="icon">
                    <i class="bi bi-exclamation-octagon-fill"></i>
                </div>
                <a href="{{ route('reports.subscriptions.expiring', ['days' => 7]) }}" class="small-box-footer">
                    Filtrar por 7 días <i class="bi bi-arrow-right-circle ms-1"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 col-12 mb-3">
            <div class="small-box bg-warning shadow-sm">
                <div class="inner text-dark">
                    <h3 class="text-dark">{{ $counts[15] ?? 0 }}</h3>
                    <p class="mb-0 text-dark">Vencen en 15 días o menos</p>
                    <small class="text-muted">Aviso preventivo de renovación</small>
                </div>
                <div class="icon">
                    <i class="bi bi-clock-history"></i>
                </div>
                <a href="{{ route('reports.subscriptions.expiring', ['days' => 15]) }}" class="small-box-footer text-dark">
                    Filtrar por 15 días <i class="bi bi-arrow-right-circle ms-1"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-4 col-md-12 col-12 mb-3">
            <div class="small-box bg-info shadow-sm">
                <div class="inner">
                    <h3>{{ $counts[30] ?? 0 }}</h3>
                    <p class="mb-0">Vencen en 30 días o menos</p>
                    <small>Planificación del mes en curso</small>
                </div>
                <div class="icon">
                    <i class="bi bi-calendar3"></i>
                </div>
                <a href="{{ route('reports.subscriptions.expiring', ['days' => 30]) }}" class="small-box-footer">
                    Filtrar por 30 días <i class="bi bi-arrow-right-circle ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Contenedor principal de la tabla con filtros -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <!-- Selector rápido de rango de días -->
                <div class="btn-group" role="group" aria-label="Filtro de días">
                    <a href="{{ route('reports.subscriptions.expiring', ['days' => 7, 'search' => $search]) }}"
                        class="btn btn-sm {{ $days === 7 ? 'btn-danger' : 'btn-outline-danger' }}">
                        <i class="bi bi-calendar-event me-1"></i> 7 Días ({{ $counts[7] ?? 0 }})
                    </a>
                    <a href="{{ route('reports.subscriptions.expiring', ['days' => 15, 'search' => $search]) }}"
                        class="btn btn-sm {{ $days === 15 ? 'btn-warning text-dark font-weight-bold' : 'btn-outline-warning text-dark' }}">
                        <i class="bi bi-calendar-event me-1"></i> 15 Días ({{ $counts[15] ?? 0 }})
                    </a>
                    <a href="{{ route('reports.subscriptions.expiring', ['days' => 30, 'search' => $search]) }}"
                        class="btn btn-sm {{ $days === 30 ? 'btn-info' : 'btn-outline-info' }}">
                        <i class="bi bi-calendar-event me-1"></i> 30 Días ({{ $counts[30] ?? 0 }})
                    </a>
                </div>

                <!-- Buscador por cliente o plan -->
                <form method="GET" action="{{ route('reports.subscriptions.expiring') }}" class="d-flex align-items-center gap-2">
                    <input type="hidden" name="days" value="{{ $days }}">
                    <div class="input-group input-group-sm" style="min-width: 260px;">
                        <input type="text" name="search" class="form-control" placeholder="Buscar por cliente o plan..."
                            value="{{ $search }}">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                    @if (!empty($search))
                        <a href="{{ route('reports.subscriptions.expiring', ['days' => $days]) }}" class="btn btn-outline-secondary btn-sm" title="Limpiar búsqueda">
                            Limpiar
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th>Cliente</th>
                        <th>Plan</th>
                        <th>Fecha de Inicio</th>
                        <th>Vencimiento</th>
                        <th>Días Restantes</th>
                        <th>Estado</th>
                        <th class="text-end" style="width: 140px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($subscriptions as $subscription)
                        @php
                            $expiration = \Carbon\Carbon::parse($subscription->expiration_date)->startOfDay();
                            $daysRemaining = (int) today()->diffInDays($expiration, false);
                        @endphp
                        <tr>
                            <td class="text-muted font-monospace">{{ $subscription->id }}</td>
                            <td>
                                <div class="font-weight-bold text-dark">{{ $subscription->client->name ?? 'Sin cliente' }}</div>
                                <div class="small text-muted">
                                    {{ $subscription->client->phone ?? '' }}
                                    @if(!empty($subscription->client->email))
                                        • {{ $subscription->client->email }}
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $subscription->plan->nombre ?? 'Sin plan' }}
                                </span>
                                <div class="small text-muted mt-1">
                                    Bs. {{ number_format((float) ($subscription->plan->precio ?? 0), 2) }}
                                </div>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($subscription->start_date)->format('d/m/Y') }}</td>
                            <td>
                                <strong>{{ $expiration->format('d/m/Y') }}</strong>
                            </td>
                            <td>
                                @if ($daysRemaining <= 0)
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-clock me-1"></i> Vence hoy
                                    </span>
                                @elseif ($daysRemaining <= 7)
                                    <span class="badge bg-danger shadow-sm">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $daysRemaining }} {{ $daysRemaining === 1 ? 'día' : 'días' }}
                                    </span>
                                @elseif ($daysRemaining <= 15)
                                    <span class="badge bg-warning text-dark shadow-sm">
                                        <i class="bi bi-clock-fill me-1"></i> {{ $daysRemaining }} días
                                    </span>
                                @else
                                    <span class="badge bg-info shadow-sm">
                                        <i class="bi bi-info-circle-fill me-1"></i> {{ $daysRemaining }} días
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-success">
                                    {{ $subscription->status->label() }}
                                </span>
                            </td>
                            <td class="text-end">
                                @can('payments.create')
                                    <a href="{{ route('payments.create') }}" class="btn btn-outline-success btn-sm" title="Registrar pago">
                                        <i class="bi bi-cash me-1"></i> Pagar
                                    </a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox display-4 d-block mb-2 text-secondary"></i>
                                No se encontraron suscripciones activas por vencer en el rango de <strong>{{ $days }} días</strong>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($subscriptions->hasPages())
            <div class="card-footer bg-white d-flex justify-content-between align-items-center flex-wrap py-3">
                <div class="small text-muted mb-2 mb-md-0">
                    Mostrando {{ $subscriptions->firstItem() }} a {{ $subscriptions->lastItem() }} de {{ $subscriptions->total() }} registros
                </div>
                <div>
                    {{ $subscriptions->links() }}
                </div>
            </div>
        @endif
    </div>
@stop
