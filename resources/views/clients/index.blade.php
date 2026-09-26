@extends('adminlte::page')

@section('title', 'Clients')

@section('content_header')
    <h1>Clientes</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <a href="{{ route('clients.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Nuevo Cliente
        </a>
    </div>

    <div class="card-body table-responsive p-0">
        <div id="alert-container"></div>

        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Telefono</th>
                    <th>Direccion</th>
                    <th style="width: 200px">Accion</th>
                </tr>
            </thead>
            <tbody id="clients-table-body">
                @forelse ($clients as $client)
                    <tr id="client-row-{{ $client->id }}">
                        <td>{{ $client->id }}</td>
                        <td>{{ $client->name }}</td>
                        <td>{{ $client->email }}</td>
                        <td>{{ $client->phone ?? '-' }}</td>
                        <td>{{ $client->address ?? '-' }}</td>
                        <td>
                            <a href="{{ route('clients.edit', $client) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <button type="button" class="btn btn-danger btn-sm"
                                    onclick="confirmDelete({{ $client->id }}, '{{ route('clients.destroy', $client) }}')">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No hay clientes registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-footer">
        {{ $clients->links() }}
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@vite(['resources/js/clients/index.js'])
@endsection
