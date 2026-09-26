@extends('adminlte::page')

@section('title', 'Users')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Users</h3>

            <div class="card-tools">
                <button class="btn btn-primary">
                    New User
                </button>
            </div>
        </div>

        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Updated At</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td>Test User</td>
                        <td>test@example.com</td>
                        <td>Admin</td>
                        <td>26/09/2026</td>
                        <td>
                            <button class="btn btn-sm btn-warning">
                                Edit
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection