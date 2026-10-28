@extends('adminlte::page')

@section('title', 'Mi perfil')

@section('content')
    <h3>Mi perfil</h3>

    <form id="edit-form" data-update-url="{{ route('profile.update') }}" data-index-url="{{ route('profile.edit') }}">

        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" name="name" class="form-control" value="{{ $user->name }}">
            <div class="invalid-feedback"></div>
        </div>

        <div class="mb-3">
            <label class="form-label">Correo electrónico</label>
            <input type="email" name="email" class="form-control" value="{{ $user->email }}">
            <div class="invalid-feedback"></div>
        </div>

        <hr>
        <p class="text-muted">Déjalo en blanco si no quieres cambiar la contraseña.</p>

        <div class="mb-3">
            <label class="form-label">Contraseña actual</label>
            <input type="password" name="current_password" class="form-control" autocomplete="current-password">
            <div class="invalid-feedback"></div>
        </div>

        <div class="mb-3">
            <label class="form-label">Nueva contraseña</label>
            <input type="password" name="password" class="form-control" autocomplete="new-password">
            <div class="invalid-feedback"></div>
        </div>

        <div class="mb-3">
            <label class="form-label">Confirmar nueva contraseña</label>
            <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password">
            <div class="invalid-feedback"></div>
        </div>

        <button type="submit" class="btn btn-success">Guardar cambios</button>
    </form>
@endsection

@section('js')
    @vite(['resources/js/profile/edit.js'])
@endsection