@extends('adminlte::page')

@section('title', 'Recibo de Pago #' . $payment->id)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center d-print-none">
        <h1>
            Recibo de Pago #{{ $payment->id }}
        </h1>

        <div>
            <button
                type="button"
                class="btn btn-primary btn-sm mr-1"
                data-action="print-receipt"
            >
                <i class="fas fa-print mr-1"></i>
                Imprimir
            </button>

            <a
                href="{{ route('payments.index') }}"
                class="btn btn-secondary btn-sm"
            >
                <i class="fas fa-arrow-left mr-1"></i>
                Volver al Listado
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8 offset-md-2">

            <x-payment-receipt
                :payment="$payment"
            />
<button
    type="button"
    class="btn btn-danger btn-sm mr-1"
    data-action="download-receipt"
    data-url="{{ route('payments.receipt.pdf', $payment) }}"
    data-filename="recibo-pago-{{ $payment->id }}.pdf"
>
    <i class="fas fa-file-pdf mr-1"></i>
    Descargar PDF
</button>
            <div class="row no-print mt-3 d-print-none">
                <div class="col-12">
                    <button
                        type="button"
                        class="btn btn-primary float-right"
                        data-action="print-receipt"
                    >
                        <i class="fas fa-print mr-1"></i>
                        Imprimir Recibo
                    </button>

                    <a
                        href="{{ route('payments.index') }}"
                        class="btn btn-secondary float-right mr-2"
                    >
                        Volver al Listado
                    </a>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('js')
    @vite(['resources/js/payments/receipt.js'])
@endsection