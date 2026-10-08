@extends('adminlte::page')

@section('title', 'Suscripciones')

@section('content_header')
    <h1>Suscripciones</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <a href="{{ route('subscriptions.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Nueva Suscripción
        </a>
    </div>

    <div class="card-body table-responsive p-0">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Plan</th>
                    <th>Inicio</th>
                    <th>Fin (Vence)</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody id="subscriptions-table-body">
                <tr>
                    <td colspan="5" class="text-center">Cargando suscripciones...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('js')
@vite(['resources/js/subscriptions/index.js'])
@endsection
