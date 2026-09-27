@extends('adminlte::page')

@section('title', 'Roles')

@section('content')
    <h4>Roles</h4>

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
                        <a href="{{ route('roles.edit', $role) }}">Edit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection