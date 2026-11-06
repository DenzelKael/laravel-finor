@extends('adminlte::page')

@section('title', __('adminlte.activity_log'))

@section('content_header')
<div class="d-flex flex-column">
    <h1 class="m-0">{{ __('Bitácora de auditoría') }}</h1>
    <small class="text-muted mt-1">
        Consulta las acciones realizadas por los usuarios del sistema.
    </small>
</div>
@stop

@section('content')

@php
    $eventos = [
        'auth.login' => 'Inicio de sesión',
        'auth.logout' => 'Cierre de sesión',
        'auth.failed' => 'Intento fallido',
        'role.created' => 'Rol creado',
        'role.deleted' => 'Rol eliminado',
        'role.attached' => 'Rol asignado',
        'role.detached' => 'Rol quitado',
        'permission.created' => 'Permiso creado',
        'permission.deleted' => 'Permiso eliminado',
        'permission.attached' => 'Permisos asignados',
        'permission.detached' => 'Permisos quitados',
    ];
@endphp

<x-adminlte-card icon="bi bi-funnel" title="Filtros de búsqueda" class="mb-3">
    <form method="GET" action="{{ route('adminlte.activity.index') }}">
        <div class="row g-3">
            <div class="col-md-3">
                <label for="user_id" class="form-label">Usuario</label>
                <select id="user_id" name="user_id" class="form-select">
                    <option value="">Todos</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label for="from" class="form-label">Desde</label>
                <input id="from" type="date" name="from" class="form-control" value="{{ request('from') }}">
            </div>

            <div class="col-md-3">
                <label for="to" class="form-label">Hasta</label>
                <input id="to" type="date" name="to" class="form-control" value="{{ request('to') }}">
            </div>

            <div class="col-md-3 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Filtrar
                </button>

                <a href="{{ route('adminlte.activity.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Limpiar
                </a>
            </div>
        </div>
    </form>
</x-adminlte-card>

@if ($errors->any())
    <div class="alert alert-danger">
        {{ $errors->first() }}
    </div>
@endif

<x-adminlte-card icon="bi bi-clock-history" title="Registro de actividad" bodyClass="p-0">
    <div class="table-responsive">
        <table class="table table-striped align-middle m-0">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Evento</th>
                    <th>Descripción</th>
                    <th>IP</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($activities as $activity)
                    <tr>
                        <td>{{ $activity->user?->name ?? 'Sistema' }}</td>
                        <td>
                            <span class="badge text-bg-secondary">
                                {{ $eventos[$activity->event] ?? $activity->event }}
                            </span>
                        </td>
                        <td>{{ $activity->description }}</td>
                        <td class="text-secondary">{{ $activity->ip_address }}</td>
                        <td class="text-secondary">{{ $activity->created_at?->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-secondary py-4">
                            No hay actividad registrada.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($activities->hasPages())
        <x-slot name="footer">
            {{ $activities->links() }}
        </x-slot>
    @endif
</x-adminlte-card>

@stop