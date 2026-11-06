@extends('adminlte::page')

@section('title', 'Clients')

@section('content_header')
    <h1>Clientes</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex flex-wrap align-items-center" style="gap: .5rem">
            <a href="{{ route('clients.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Nuevo Cliente
            </a>
            <input type="search" id="client-search" class="form-control form-control-sm"
                   style="max-width: 280px" placeholder="Buscar por nombre o correo">
            <select id="client-status" class="form-control form-control-sm" style="max-width: 200px">
                <option value="">Todos los estados</option>
                @foreach (\App\Enums\ClientStatus::cases() as $status)
                    <option value="{{ $status->value }}">{{ $status->label() }}</option>
                @endforeach
            </select>
            <button type="button" id="client-export" class="btn btn-outline-success btn-sm ml-auto">
                <i class="fas fa-file-csv"></i> Exportar CSV
            </button>
        </div>
    </div>

    <div class="card-body table-responsive p-0">
        <table id="clients-table" class="table table-bordered table-hover"
               data-base-url="{{ route('clients.index', [], false) }}"
               data-can-update="@json(auth()->user()->can('clients.update'))"
               data-can-delete="@json(auth()->user()->can('clients.delete'))">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Telefono</th>
                    <th>Direccion</th>
                    <th>Estado</th>
                    <th style="width: 260px">Accion</th>
                </tr>
            </thead>
            <tbody id="clients-table-body">
                <tr>
                    <td colspan="7" class="text-center">Cargando clientes...</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="card-footer">
        <div id="clients-pagination" class="d-flex align-items-center"></div>
    </div>
</div>
@endsection

@section('js')
@vite(['resources/js/clients/index.js'])
@endsection