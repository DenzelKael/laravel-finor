@extends('adminlte::page')

@section('title', 'New Client')

@section('content_header')
    <h1>Nuevo Cliente</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form id="create-form"
              data-store-url="{{ route('clients.store') }}"
              data-index-url="{{ route('clients.index') }}">
            @csrf
            @include('clients.form')

            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="{{ route('clients.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
@endsection

@section('js')
@vite(['resources/js/clients/create.js'])
@endsection
