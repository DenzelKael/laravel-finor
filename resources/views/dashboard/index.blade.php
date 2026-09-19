@extends('adminlte::page')

@section('title', 'Main Dashboard')

@section('content_header')
    <h1>Main Dashboard</h1>
@stop

@section('content')
    <div class="row">
        <!-- Active Clients Card -->
        <div class="col-lg-4 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $clientesActivos }}</h3>
                    <p>Active Clients</p>
                </div>
                <div class="icon"><i class="fas fa-users"></i></div>
            </div>
        </div>
        
        <!-- Monthly Revenue Card -->
        <div class="col-lg-4 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>${{ number_format($ingresosMes, 2) }}</h3>
                    <p>Monthly Revenue</p>
                </div>
                <div class="icon"><i class="fas fa-dollar-sign"></i></div>
            </div>
        </div>
        
        <!-- Expiring Subscriptions Card -->
        <div class="col-lg-4 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $suscripcionesPorVencer }}</h3>
                    <p>Expiring Subscriptions</p>
                </div>
                <div class="icon"><i class="fas fa-clock"></i></div>
            </div>
        </div>
    </div>

    <!-- Chart Container -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Activity Overview</h3>
                </div>
                <div class="card-body">
                    <canvas id="mainChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <!-- Chart.js and DOM/Fetch Implementation -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fetch API to get chart data
            fetch('/api/chart-data')
                .then(response => response.json())
                .then(data => {
                    const ctx = document.getElementById('mainChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'line', 
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: 'Revenue ($)',
                                data: data.data,
                                backgroundColor: 'rgba(60,141,188,0.2)',
                                borderColor: 'rgba(60,141,188,1)',
                                borderWidth: 2,
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false
                        }
                    });
                })
                .catch(error => console.error('Error fetching chart data:', error));
        });
    </script>
@stop