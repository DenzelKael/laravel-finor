@extends('adminlte::page')

@section('title', 'Edit Role')

@php
    use Illuminate\Support\Str;
@endphp

@section('content')

    <h3>Editar Rol: {{ $role->name }}</h3>
    <form id="edit-form" data-update-url="{{ route('roles.update', $role) }}" data-index-url="{{ route('roles.index') }}">
        @csrf
        @method('PUT')

        <div class="card">
            <div class="card-body">
                @foreach ($permissions->groupBy(fn($p) => Str::before($p->name, '.')) as $module => $items)

                    <div class="d-flex align-items-center justify-content-between mt-3">
                        <h5 class="text-capitalize mb-0">
                            {{ $module }}
                        </h5>

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
                                name="permissions[]" value="{{ $permission->name }}"
                                @checked($role->hasPermissionTo($permission->name))>

                            <label class="form-check-label">
                                {{ $permission->name }}
                            </label>
                        </div>
                    @endforeach

                @endforeach
            </div>
        </div>

        <button type="submit" class="btn btn-success mt-3">
            Guardar Cambios
        </button>
        <a href="{{ route('roles.index') }}" class="btn btn-secondary mt-3">
            Volver
        </a>
    </form>

@endsection

@section('js')
    @vite(['resources/js/roles/edit.js'])
@endsection