@extends('adminlte::page')

@section('title', 'Página no encontrada')

@section('content')
    <div class="text-center mt-5">
        <h1 class="display-1 text-danger">404</h1>

        <h3>Página no encontrada</h3>

        <p>
            La página que estás buscando no existe.
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