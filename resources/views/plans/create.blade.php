@extends('adminlte::page')

@section('title', 'New Plan')

@section('content_header')
    <h1>Nuevo Plan</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form id="plan-form"
              data-store-url="{{ route('plans.store') }}"
              data-index-url="{{ route('plans.index') }}">
            @csrf
            @include('plans.form')

            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="{{ route('plans.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
@endsection

@push('js')
@vite('resources/js/pages/plan-form.js')
@endpush
