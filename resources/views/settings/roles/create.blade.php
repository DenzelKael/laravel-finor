@extends('adminlte::page')

@section('title', 'Create Role')

@php
    use Illuminate\Support\Str;
@endphp

@section('content')

    <h3>Crear Rol</h3>

    <form id="create-form" data-store-url="{{ route('roles.store') }}" data-index-url="{{ route('roles.index') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">Nombre del Rol</label>
            <input type="text" name="name" class="form-control" placeholder="Contador">
            <div class="invalid-feedback"></div>
        </div>

        <div class="card">
            <div class="card-body">
                @foreach ($permissions->groupBy(fn($p) => Str::before($p->name, '.')) as $module => $items)

                    <div class="d-flex align-items-center justify-content-between mt-3">
                        <h5 class="text-capitalize mb-0">{{ $module }}</h5>

                        <div class="form-check">
                            <input type="checkbox" class="form-check-input module-toggle" data-module="{{ $module }}"
                                id="toggle-{{ $module }}">
                            <label class="form-check-label" for="toggle-{{ $module }}">
                                Marcar todos
                            </label>
                        </div>
                    </div>

                    @foreach ($items as $permission)
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input permission-checkbox" data-module="{{ $module }}"
                                name="permissions[]" value="{{ $permission->name }}">
                            <label class="form-check-label">{{ $permission->name }}</label>
                        </div>
                    @endforeach

                @endforeach
            </div>
        </div>

        <button type="submit" class="btn btn-success mt-3">Guardar</button>
        <a href="{{ route('roles.index') }}" class="btn btn-secondary mt-3">Volver</a>
    </form>

@endsection

@section('js')
    @vite(['resources/js/roles/create.js'])
@endsection