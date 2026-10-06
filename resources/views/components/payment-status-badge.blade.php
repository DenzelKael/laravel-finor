@props(['status'])

@php
    $badgeClass = match ($status?->value ?? (string) $status) {
        \App\Enums\PaymentStatus::Registered->value, 'REGISTERED' => 'badge-success',
        \App\Enums\PaymentStatus::Cancelled->value, 'CANCELLED' => 'badge-danger',
        default => 'badge-secondary',
    };

    $label = match ($status?->value ?? (string) $status) {
        \App\Enums\PaymentStatus::Registered->value, 'REGISTERED' => 'Registrado',
        \App\Enums\PaymentStatus::Cancelled->value, 'CANCELLED' => 'Anulado',
        default => (string) $status,
    };
@endphp

<span {{ $attributes->merge(['class' => 'badge ' . $badgeClass]) }}>
    {{ $label }}
</span>
