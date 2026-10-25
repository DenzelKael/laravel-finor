@extends('adminlte::page')

@section('title', 'Error interno del servidor')

@section('content')
    <div class="text-center mt-5">
        <h1 class="display-1 text-danger">500</h1>

        <h3>Error interno del servidor</h3>

        <p>
            Ha ocurrido un error inesperado.
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