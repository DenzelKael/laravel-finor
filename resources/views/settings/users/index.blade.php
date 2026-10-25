@extends('adminlte::page')

@section('title', 'Users')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">

        <div class="d-flex align-items-center gap-2">
            <h3 class="mb-0 me-3">Usuarios</h3>

            <form method="GET" class="d-flex">
                <input type="text" name="q" class="form-control" style="width: 300px"
                    placeholder="Buscar por nombre o correo" value="{{ request('q') }}">

                <button type="submit" class="btn btn-primary ms-2">
                    Buscar
                </button>
            </form>
        </div>

        @can('users.create')
            <a href="{{ route('users.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Añadir usuario
            </a>
        @endcan

    </div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->getRoleNames()->implode(', ') }}</td>
                    <td>
                        @can('users.update')
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-primary btn-sm">
                                Edit
                            </a>
                        @endcan
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $users->links() }}
@endsection

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif