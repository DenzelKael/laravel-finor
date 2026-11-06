@extends('adminlte::page')

@section('title', 'Roles')

@section('content')
    <h4>Roles</h4>

    <div class="mb-3">
        @can('roles.create')
            <a href="{{ route('roles.create') }}" class="btn btn-success">Nuevo rol</a>
        @endcan
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Rol</th>
                <th>Usuarios</th>
                <th>Permisos</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($roles as $role)
                <tr>
                    <td>{{ $role->name }}</td>
                    <td>{{ $role->users->count() }}</td>
                    <td>{{ $role->permissions->count() }}</td>
                    <td>
                        @can('roles.update')
                            <a href="{{ route('roles.edit', $role) }}" class="btn btn-primary btn-sm">
                                Edit
                            </a>
                        @endcan
                        @can('roles.delete')
                            @if ($role->name !== \App\Enums\RoleName::Admin->value)
                                <button type="button" class="btn btn-danger btn-sm js-delete-role"
                                    data-url="{{ route('roles.destroy', $role) }}">
                                    Delete
                                </button>
                            @endif
                        @endcan
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $roles->links() }}
@endsection

@section('js')
    @vite(['resources/js/roles/index.js'])
@endsection