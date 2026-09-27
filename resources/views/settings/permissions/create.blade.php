@extends('adminlte::page')

@section('title', 'Create Permission')

@section('content')

    <h3>Create Permission</h3>

    <form action="{{ route('permissions.store') }}" method="POST">

        @csrf

        <div class="mb-3">
            <label class="form-label">Permission Name</label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                placeholder="clients.view" value="{{ old('name') }}">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-success">
            Save
        </button>

        <a href="{{ route('permissions.index') }}" class="btn btn-secondary">
            Back
        </a>

    </form>

@endsection