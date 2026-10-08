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

    <!-- Gráfico con data-url
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Resumen de Actividad</h3>
                </div>
                <div class="card-body">
                    <canvas id="mainChart" data-url="{{ route('dashboard.chart') }}" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    @vite('resources/js/dashboard/main.js')
@stop