@extends('adminlte::page')

@section('title', 'Añadir usuario')

@section('content')
    <div class="container-fluid">
        <h3>Añadir usuario</h3>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <form action="{{ route('users.store') }}" method="POST" autocomplete="off">
                    @csrf

                    <div class="form-group">
                        <label for="name">Nombre</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}"
                            autocomplete="off" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Correo electrónico</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}"
                            autocomplete="off" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <input type="password" name="password" id="password" class="form-control"
                            autocomplete="new-password" required>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Confirmar contraseña</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control"
                            autocomplete="new-password" required>
                    </div>

                    <div class="form-group">
                        <label for="role">Rol</label>
                        <select name="role" id="role" class="form-control" required>
                            <option value="">Selecciona un rol</option>

                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}" @selected(old('role') == $role->name)>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Guardar usuario
                    </button>

                    <a href="{{ route('users.index') }}" class="btn btn-secondary">
                        Cancelar
                    </a>
                </form>
            </div>
        </div>
    </div>
@endsection