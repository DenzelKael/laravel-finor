@extends('adminlte::page')

@section('title', 'Edit Role')

@php
    use Illuminate\Support\Str;
@endphp

@section('content')

    <h3>Edit Role: {{ $role->name }}</h3>
    <form id="edit-form" data-update-url="{{ route('roles.update', $role) }}" data-index-url="{{ route('roles.index') }}">
        @csrf
        @method('PUT')

        <div class="card">
            <div class="card-body">
                @foreach ($permissions->groupBy(fn($p) => Str::before($p->name, '.')) as $module => $items)

                    <h5 class="mt-3 text-capitalize">
                        {{ $module }}
                    </h5>

                    @foreach ($items as $permission)
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="permissions[]" value="{{ $permission->name }}"
                                @checked($role->hasPermissionTo($permission->name))>
                            <label class="form-check-label">
                                {{ $permission->name }}
                            </label>
                        </div>
                    @endforeach
                @endforeach
            </div>
        </div>

        <button type="submit" class="btn btn-success mt-3">
            Save Changes
        </button>
        <a href="{{ route('roles.index') }}" class="btn btn-secondary mt-3">
            Back
        </a>
    </form>

@endsection

@section('js')
    @vite(['resources/js/roles/edit.js'])
@endsection