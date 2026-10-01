@extends('adminlte::page')
@section('title', 'Permissions')
@section('content')
    <h3>Permisos</h3>
    <div class="mb-3">
        @can('permissions.create')
            <a href="{{ route('permissions.create') }}" class="btn btn-success">
                Nuevo permiso
            </a>
        @endcan
    </div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Permisos</th>
                @foreach ($roles as $role)
                    <th>{{ $role->name }}</th>
                @endforeach
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($permissions as $permission)
                <tr>
                    <td>{{ $permission->name }}</td>
                    @foreach ($roles as $role)
                        <td class="text-center">
                            @if ($role->hasPermissionTo($permission->name))
                                ☑
                            @else
                                ☐
                            @endif
                        </td>
                    @endforeach
                    <td class="text-center">
                        @can('permissions.update')
                            <a href="{{ route('permissions.edit', $permission) }}" class="btn btn-sm btn-secondary">
                                Edit
                            </a>
                        @endcan
                        <form action="{{ route('permissions.destroy', $permission) }}" method="POST" style="display:inline;">

                            @csrf
                            @method('DELETE')
                            @can('permissions.delete')
                                <button type="submit" class="btn btn-danger btn-sm"
                                    onclick="return confirm('¿Eliminar este permiso?')">
                                    Delete
                                </button>
                            @endcan
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $permissions->links() }}
@endsection