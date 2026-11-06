@props(['status'])

@php
    $statusValue = $status instanceof \App\Enums\PaymentStatus
        ? $status->value
        : (string) $status;

    $badgeClass = match ($statusValue) {
        \App\Enums\PaymentStatus::Registered->value => 'badge-success',
        \App\Enums\PaymentStatus::Voided->value => 'badge-danger',
        default => 'badge-secondary',
    };

    $label = match ($statusValue) {
        \App\Enums\PaymentStatus::Registered->value => 'Registrado',
        \App\Enums\PaymentStatus::Voided->value => 'Anulado',
        default => 'Desconocido',
    };
@endphp

<span {{ $attributes->merge(['class' => 'badge ' . $badgeClass]) }}>
    {{ $label }}
</span>