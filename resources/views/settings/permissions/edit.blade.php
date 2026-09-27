@extends('adminlte::page')

@section('title', 'Edit Permission')

@section('content')

    <h3>Edit Permission</h3>

    <form action="{{ route('permissions.update', $permission) }}" method="POST">

        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Permission Name</label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                value="{{ old('name', $permission->name) }}">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-success">
            Save Changes
        </button>

        <a href="{{ route('permissions.index') }}" class="btn btn-secondary">
            Back
        </a>

    </form>

@endsection