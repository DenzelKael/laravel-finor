@extends('adminlte::page')

@section('title', 'Panel Principal')

@section('content_header')
<h1>Panel Principal</h1>
@stop

@section('content')
<div class="row">
    <!-- Tarjeta de Clientes -->
    <div class="col-lg-4 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $activeClients }}</h3>
                <p>Total de Clientes</p>
            </div>
            <div class="icon"><i class="fas fa-users"></i></div>
        </div>
    </div>

    <!-- Tarjeta de Ingresos -->
    <div class="col-lg-4 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>Bs. {{ number_format($monthlyRevenue, 2) }}</h3>
                <p>Ingresos Mensuales</p>
            </div>
            <div class="icon"><i class="fas fa-dollar-sign"></i></div>
        </div>
    </div>

    <!-- Tarjeta de Suscripciones -->
    <div class="col-lg-4 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $expiringSubscriptions }}</h3>
                <p>Suscripciones por Vencer</p>
            </div>
            <div class="icon"><i class="fas fa-clock"></i></div>
        </div>
    </div>
</div>

<!-- Notificaciones de suscripciones -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-bell mr-2"></i>
                    Notificaciones de suscripciones
                </h3>

                <div class="card-tools">
                    <span class="badge badge-warning">
                        {{ $unreadNotifications }} sin leer
                    </span>
                </div>
            </div>

            <div class="card-body p-0">
                @forelse ($notifications as $notification)
                    <div class="p-3 border-bottom
                        {{ $notification->read_at ? '' : 'bg-light' }}">

                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong>
                                    {{ $notification->data['message'] ?? 'Suscripción próxima a vencer' }}
                                </strong>

                                <p class="mb-1 mt-2">
                                    <strong>Cliente:</strong>
                                    {{ $notification->data['client_name'] ?? 'No disponible' }}
                                </p>

                                <p class="mb-1">
                                    <strong>Plan:</strong>
                                    {{ $notification->data['plan_name'] ?? 'No disponible' }}
                                </p>

                                <p class="mb-1">
                                    <strong>Fecha de vencimiento:</strong>
                                    {{ $notification->data['expiration_date'] ?? 'No disponible' }}
                                </p>

                                <small class="text-muted">
                                    {{ $notification->created_at->format('d/m/Y H:i') }}
                                </small>
                            </div>

                            <span class="badge {{ $notification->read_at ? 'badge-secondary' : 'badge-warning' }}">
                                {{ $notification->read_at ? 'Leída' : 'Pendiente' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-muted">
                        <i class="fas fa-bell-slash fa-2x mb-2"></i>
                        <p class="mb-0">
                            No tienes notificaciones de suscripciones.
                        </p>
                    </div>
                @endforelse
            </div>

            @if ($notifications->isNotEmpty())
                <div class="card-footer text-muted">
                    Mostrando las últimas {{ $notifications->count() }} notificaciones.
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Gráfico con data-url -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Resumen de Actividad</h3>
            </div>
            <div class="card-body">
                <div id="mainChart" data-url="{{ route('dashboard.chart') }}" style="min-height: 250px;"></div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
@vite('resources/js/dashboard/main.js')
@stop