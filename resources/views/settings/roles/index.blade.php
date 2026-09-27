@extends('adminlte::page')

@section('title', 'Roles')

@section('content')
    <h3>Roles</h3>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Role</th>
                <th>Users</th>
                <th>Permissions</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($roles as $role)
                <tr>
                    <td>{{ $role->name }}</td>
                    <td>{{ $role->users->count() }}</td>
                    <td>{{ $role->permissions->count() }}</td>
                    <td>
                        <button class="btn btn-primary btn-sm">
                            Edit
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection