@extends('adminlte::page')

@section('title', 'Planes de Suscripción')

@section('content_header')
    <h1>Planes de Suscripción</h1>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('plans.create') }}" class="btn btn-primary mb-3">
        <i class="fas fa-plus"></i> Nuevo Plan
    </a>

    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th>Duración (días)</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($plans as $plan)
                        <tr>
                            <td>{{ $plan->nombre }}</td>
                            <td>${{ number_format($plan->precio, 2) }}</td>
                            <td>{{ $plan->duracion_dias }}</td>
                            <td>
                                @if ($plan->activo)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-secondary">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('plans.edit', $plan) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>editar
                                </a>
                                <form action="{{ route('plans.destroy', $plan) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('¿Eliminar este plan?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $plans->links() }}
        </div>
    </div>
@stop