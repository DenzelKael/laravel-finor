@extends('adminlte::page')

@section('title', 'Edit Plan')

@section('content_header')
    <h1>Editar Plan</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form id="plan-form"
              data-update-url="{{ route('plans.update', $plan) }}"
              data-index-url="{{ route('plans.index') }}">
            @csrf
            @include('plans.form')

            <button type="submit" class="btn btn-primary">Editar</button>
            <a href="{{ route('plans.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
@endsection

@push('js')
@vite('resources/js/pages/plan-form.js')
@endpush
