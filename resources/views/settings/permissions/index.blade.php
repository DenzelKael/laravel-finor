@extends('adminlte::page')

@section('title', 'Permissions')

@section('content')
    <h3>Permissions</h3>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Permission</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($permissions as $permission)
                <tr>
                    <td>{{ $permission->name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection