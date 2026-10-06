@extends('adminlte::page')

@section('title', 'Edit Permission')

@section('content')

    <h3>Editar Permiso</h3>

    <form id="edit-form" data-update-url="{{ route('permissions.update', $permission) }}"
        data-index-url="{{ route('permissions.index') }}">

        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Nombre del Permiso</label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                value="{{ old('name', $permission->name) }}">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-success">
            Guardar Cambios
        </button>

        <a href="{{ route('permissions.index') }}" class="btn btn-secondary">
            Volver
        </a>

    </form>

@endsection

@section('js')
    @vite(['resources/js/permissions/edit.js'])
@endsection