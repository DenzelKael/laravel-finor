@extends('adminlte::page')
@section('title', 'Permissions')
@section('content')
    <h3>Permissions</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Permission</th>
                @foreach ($roles as $role)
                    <th>{{ $role->name }}</th>
                @endforeach
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
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection