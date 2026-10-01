@extends('adminlte::page')

@section('title', 'Edit Client')

@section('content_header')
    <h1>Editar Cliente</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form id="edit-form"
              data-update-url="{{ route('clients.update', $client) }}"
              data-index-url="{{ route('clients.index') }}">
            @csrf
            @include('clients.form')

            <button type="submit" class="btn btn-primary">Editar</button>
            <a href="{{ route('clients.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
@endsection

@section('js')
@vite(['resources/js/clients/edit.js'])
@endsection
