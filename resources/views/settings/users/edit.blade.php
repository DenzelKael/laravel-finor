@extends('adminlte::page')

@section('title', 'Edit User')

@section('content')

    <h3>Edit User: {{ $user->name }}</h3>

    <p>Email: {{ $user->email }}</p>

    <form id="edit-form" data-update-url="{{ route('users.update', $user) }}" data-index-url="{{ route('users.index') }}">
        @csrf
        @method('PUT')

        @foreach ($roles as $role)
            <div class="form-check">
                <input type="radio" class="form-check-input" name="role" value="{{ $role->name }}" id="role-{{ $role->id }}"
                    @checked($user->hasRole($role->name))>
                <label class="form-check-label" for="role-{{ $role->id }}">
                    {{ $role->name }}
                </label>
            </div>
        @endforeach

        <hr>
        <h4>Direct Permissions</h4>
        @foreach ($permissions as $permission)
            <div class="form-check">
                <input type="checkbox" class="form-check-input" name="permissions[]" value="{{ $permission->name }}"
                    @checked($user->hasDirectPermission($permission->name))>
                <label>
                    {{ $permission->name }}
                </label>
            </div>
        @endforeach

        @error('role')
            <div class="alert alert-danger mt-3">
                {{ $message }}
            </div>
        @enderror

        <button type="submit" class="btn btn-success mt-3">
            Save Changes
        </button>
        <a href="{{ route('users.index') }}" class="btn btn-secondary mt-3">
            Back
        </a>
    </form>

@endsection

@section('js')
    @vite(['resources/js/users/edit.js'])
@endsection