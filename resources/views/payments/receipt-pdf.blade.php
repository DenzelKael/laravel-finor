<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <title>
        Recibo de Pago #{{ $payment->id }}
    </title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #212529;
        }

        .invoice {
            width: 100%;
        }

        .row {
            width: 100%;
            clear: both;
        }

        .col-12 {
            width: 100%;
        }

        .col-6,
        .col-sm-6 {
            width: 48%;
            display: inline-block;
            vertical-align: top;
        }

        .float-right {
            float: right;
        }

        .text-right {
            text-align: right;
        }

        .text-muted {
            color: #6c757d;
        }

        .text-success {
            color: #198754;
        }

        .font-weight-bold {
            font-weight: bold;
        }

        .h4 {
            font-size: 18px;
        }

        .lead {
            font-size: 16px;
        }

        .mt-2 {
            margin-top: 10px;
        }

        .mt-3 {
            margin-top: 15px;
        }

        .mb-3 {
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th,
        table td {
            padding: 8px;
            border: 1px solid #dee2e6;
        }

        table th {
            background-color: #f2f2f2;
        }

        address {
            font-style: normal;
            line-height: 1.5;
        }

        .badge {
            display: inline-block;
            padding: 4px 7px;
            font-size: 10px;
            font-weight: bold;
            border-radius: 3px;
        }

        .badge-success {
            border: 1px solid #198754;
        }

        .badge-danger {
            border: 1px solid #dc3545;
        }

        .badge-secondary {
            border: 1px solid #6c757d;
        }

        .alert {
            padding: 10px;
            margin-top: 15px;
            border: 1px solid #dee2e6;
        }

        .alert-danger {
            border-color: #dc3545;
        }
    </style>
</head>

<body>

    <x-payment-receipt
        :payment="$payment"
    />

</body>
</html>