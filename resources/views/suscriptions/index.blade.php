@extends('adminlte::page')

@section('title', 'Suscripciones')

@section('content_header')
    <h1>Suscripciones</h1>
@stop

@section('content')

<a href="{{ route('suscriptions.create') }}" class="btn btn-primary mb-3">
    <i class="fas fa-plus"></i> Nueva Suscripción
</a>

    <div class="card">
        <div class="card-body">

            <table class="table table-striped">

                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Plan</th>
                        <th>Fecha inicio</th>
                        <th>Fecha fin</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($suscripciones as $suscripcion)

                        <tr>
                            <td colspan="6" class="text-center">
                                Suscripción encontrada.
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="6" class="text-center">
                                No hay suscripciones registradas.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>
    </div>

@stop