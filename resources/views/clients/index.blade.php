@extends('adminlte::page')

@section('title', 'Clients')

@section('content_header')
    <h1>Clients</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <a href="{{ route('clients.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> New Client
        </a>
    </div>

    <div class="card-body table-responsive p-0">
        <div id="alert-container"></div>

        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th style="width: 200px">Actions</th>
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
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete({{ $client->id }})">
                                <i class="fas fa-trash"></i> Delete
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
@vite(['resources/js/app.js'])
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function showAlert(message, type = 'success') {
        document.getElementById('alert-container').innerHTML =
            `<div class="alert alert-${type} m-3">${message}</div>`;
        setTimeout(() => document.getElementById('alert-container').innerHTML = '', 3000);
    }

    function confirmDelete(id) {
        Swal.fire({
            title: 'Estas seguro',
            text: "Este cliente sera eliminado permanentemente.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelarButtonColor: '#6c757d',
            confirmButtonText: 'Si, eliminar'
        }).then((result) => {
            if (result.isConfirmed) {
                deleteClient(id);
            }
        });
    }

    async function deleteClient(id) {
        const { data } = await window.api.delete(`/clients/${id}`);

        if (data.success) {
            document.getElementById(`client-row-${id}`).remove();
            showAlert(data.message);
        }
    }
</script>
@endsection
