@extends('adminlte::page')

@section('title', __('dashboard.title'))

@section('content_header')
<h1>{{ __('dashboard.title') }}</h1>
@stop

@section('content')
<div class="row">
    <div class="col-lg-4 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $activeClients }}</h3>
                <p>{{ __('dashboard.active_clients') }}</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>Bs. {{ number_format($monthlyRevenue, 2) }}</h3>
                <p>{{ __('dashboard.monthly_revenue') }}</p>
            </div>
            <div class="icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $expiringSubscriptions }}</h3>
                <p>{{ __('dashboard.expiring_subscriptions') }}</p>
            </div>
            <div class="icon">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    {{ __('dashboard.activity_summary') }}
                </h3>
            </div>

            <div class="card-body">
                <div
                    id="mainChart"
                    data-url="{{ route('dashboard.chart') }}"
                    data-series-label="{{ __('dashboard.chart_revenue') }}"
                    style="min-height: 250px;"
                ></div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
@vite('resources/js/dashboard/main.js')
@stop