@extends('adminlte::page')

@section('title', 'Nueva Suscripción')

@section('content_header')
    <h1>Nueva Suscripción</h1>
@stop

@section('content')

    <div class="card">
        <div class="card-body">

            <h3>Clientes disponibles</h3>

            <p>Total de clientes: {{ $clients->count() }}</p>

            <h3>Planes disponibles</h3>

            <p>Total de planes: {{ $plans->count() }}</p>

        </div>
    </div>

@stop