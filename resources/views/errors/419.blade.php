@extends('adminlte::page')

@section('title', 'Página expirada')

@section('content')
    <div class="text-center mt-5">
        <h1 class="display-1 text-danger">419</h1>

        <h3>Página expirada</h3>

        <p>
            Tu sesión ha expirado. Vuelve a iniciar sesión e inténtalo de nuevo.
        </p>

        <a href="{{ url('/') }}" class="btn btn-primary">
            Volver al inicio
        </a>
    </div>
@endsection