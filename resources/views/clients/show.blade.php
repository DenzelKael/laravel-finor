@extends('adminlte::page')

@section('title', 'Detalle del cliente')

@section('content_header')
    <h1>{{ $client->name }}</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <a href="{{ route('clients.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
        @can('clients.update')
            <a href="{{ route('clients.edit', $client) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Editar
            </a>
        @endcan
    </div>
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-3">Email</dt>
            <dd class="col-sm-9">{{ $client->email }}</dd>
            <dt class="col-sm-3">Telefono</dt>
            <dd class="col-sm-9">{{ $client->phone ?? '-' }}</dd>
            <dt class="col-sm-3">Direccion</dt>
            <dd class="col-sm-9">{{ $client->address ?? '-' }}</dd>
            <dt class="col-sm-3">Estado</dt>
            <dd class="col-sm-9">{{ $client->status->label() }}</dd>
        </dl>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3 class="card-title">Suscripciones</h3></div>
    <div class="card-body table-responsive p-0">
        <table class="table table-bordered table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Plan</th>
                    <th>Inicio</th>
                    <th>Vencimiento</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($client->subscriptions as $subscription)
                    <tr>
                        <td>{{ $subscription->id }}</td>
                        <td>{{ $subscription->plan->nombre }}</td>
                        <td>{{ $subscription->start_date->format('d/m/Y') }}</td>
                        <td>{{ $subscription->expiration_date->format('d/m/Y') }}</td>
                        <td>{{ $subscription->status->label() }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Este cliente no tiene suscripciones.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3 class="card-title">Pagos</h3></div>
    <div class="card-body table-responsive p-0">
        <table class="table table-bordered table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Suscripcion</th>
                    <th>Fecha</th>
                    <th>Monto</th>
                    <th>Metodo</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($payments as $payment)
                    <tr>
                        <td>{{ $payment->id }}</td>
                        <td>#{{ $payment->subscription_id }}</td>
                        <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                        <td>{{ $payment->formatted_amount }}</td>
                        <td>{{ $payment->payment_method_label }}</td>
                        <td>{{ $payment->status_label }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Este cliente no tiene pagos.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection