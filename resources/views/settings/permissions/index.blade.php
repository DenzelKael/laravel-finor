@extends('adminlte::page')
@section('title', 'Permissions')
@section('content')
    <h3>Permissions</h3>
    <div class="mb-3">
        <a href="{{ route('permissions.create') }}" class="btn btn-success">
            New Permission
        </a>
    </div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Permission</th>
                @foreach ($roles as $role)
                    <th>{{ $role->name }}</th>
                @endforeach
                <th>Actions</th>
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
                        <a href="{{ route('permissions.edit', $permission) }}" class="btn btn-sm btn-secondary">
                            Edit
                        </a>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('permissions.edit', $permission) }}" class="btn btn-sm btn-secondary">
                            Edit
                        </a>
                        <form action="{{ route('permissions.destroy', $permission) }}" method="POST" style="display:inline;">

                            @csrf
                            @method('DELETE')

                            <button type="submit" class="btn btn-danger btn-sm"
                                onclick="return confirm('¿Eliminar este permiso?')">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection