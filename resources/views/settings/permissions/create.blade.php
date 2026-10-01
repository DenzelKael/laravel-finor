@extends('adminlte::page')

@section('title', 'Create Permission')

@section('content')

    <h3>Create Permission</h3>

    <form id="create-form" data-store-url="{{ route('permissions.store') }}"
        data-index-url="{{ route('permissions.index') }}">

        @csrf

        <div class="mb-3">
            <label class="form-label">Permission Name</label>
            <input type="text" name="name" class="form-control" placeholder="clients.view">

            <div class="invalid-feedback"></div>
        </div>

        <button type="submit" class="btn btn-success">
            Save
        </button>

        <a href="{{ route('permissions.index') }}" class="btn btn-secondary">
            Back
        </a>

    </form>

@endsection

@section('js')
    @vite(['resources/js/permissions/create.js'])
@endsection