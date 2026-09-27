@extends('adminlte::page')

@section('title', 'Edit User')

@section('content')

    <h3>Edit User: {{ $user->name }}</h3>

    <p>Email: {{ $user->email }}</p>

    <form action="{{ route('users.update', $user) }}" method="POST">
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

        <button type="submit" class="btn btn-success mt-3">
            Save Changes
        </button>
        <a href="{{ route('users.index') }}" class="btn btn-secondary mt-3">
            Back
        </a>
    </form>

@endsection