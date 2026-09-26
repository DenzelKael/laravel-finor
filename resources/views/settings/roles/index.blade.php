@extends('adminlte::page')

@section('title', 'Roles')

@section('content')
    <h3>Roles</h3>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Role</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($roles as $role)
                <tr>
                    <td>{{ $role->name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection