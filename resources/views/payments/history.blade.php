@extends('adminlte::page')

@section('title', $title)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1>{{ $title }}</h1>
            <p class="text-muted mb-0">{{ $subtitle }}</p>
        </div>

        <a
            href="{{ route('payments.index') }}"
            class="btn btn-secondary btn-sm"
        >
            <i class="fas fa-arrow-left mr-1"></i>
            Volver
        </a>
    </div>
@endsection

@section('content')
    <div
        id="history-alert-container"
        class="mb-3"
    ></div>

    <div class="card card-outline card-primary">
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Método</th>
                        <th>Monto</th>
                        <th>Estado</th>
                        <th>Motivo de Anulación</th>
                    </tr>
                </thead>

                <tbody
                    id="payments-history-body"
                    data-url="{{ $historyUrl }}"
                >
                    <tr>
                        <td
                            colspan="6"
                            class="text-center text-muted py-4"
                        >
                            Cargando pagos...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="card-footer d-flex justify-content-between align-items-center">
            <button
                type="button"
                id="history-prev"
                class="btn btn-secondary btn-sm"
                disabled
            >
                Anterior
            </button>

            <span id="history-page-info">
                Página 1
            </span>

            <button
                type="button"
                id="history-next"
                class="btn btn-secondary btn-sm"
                disabled
            >
                Siguiente
            </button>
        </div>
    </div>
@endsection

@section('js')
    @vite(['resources/js/payments/history.js'])
@endsection