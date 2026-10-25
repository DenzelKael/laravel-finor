@extends('adminlte::page')

@section('title', 'Acceso denegado')

@section('content')
    <div class="text-center mt-5">
        <h1 class="display-1 text-danger">403</h1>

        <h3>Acceso denegado</h3>

        <p>
            No tienes permisos para acceder a esta página.
        </p>

        <a href="{{ url('/') }}" class="btn btn-primary">
            Volver al inicio
        </a>

        <button type="button" class="btn btn-outline-secondary ml-2" onclick="history.back()">
            <i class="fas fa-arrow-left mr-1"></i>
            Volver atrás
        </button>

    </div>
@endsection