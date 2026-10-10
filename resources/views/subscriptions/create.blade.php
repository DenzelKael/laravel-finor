@extends('adminlte::page')

@section('title', 'Nueva Suscripción')

@section('content_header')
    <h1>Nueva Suscripción</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form id="subscription-form"
              data-store-url="{{ route('subscriptions.store') }}"
              data-index-url="{{ route('subscriptions.index') }}">
            @csrf

            <div class="form-group">
                <label for="client_id">Cliente</label>
                <select name="client_id" id="client_id" class="form-control" required>
                    <option value="">Seleccione un cliente</option>
                    @foreach ($clients as $client)
                        <option value="{{ $client->id }}">
                            {{ $client->name }} — {{ $client->email }}
                        </option>
                    @endforeach
                </select>
                <div class="invalid-feedback"></div>
            </div>

            <div class="form-group">
                <label for="plan_id">Plan</label>
                <select name="plan_id" id="plan_id" class="form-control" required>
                    <option value="">Seleccione un plan</option>
                    @foreach ($plans as $plan)
                        <option value="{{ $plan->id }}">
                            {{ $plan->nombre }} — Bs {{ number_format((float) $plan->precio, 2) }}
                            ({{ $plan->duracion_dias }} días)
                        </option>
                    @endforeach
                </select>
                <div class="invalid-feedback"></div>
            </div>

            <button type="submit" class="btn btn-primary">
                Registrar suscripción
            </button>

            <a href="{{ route('subscriptions.index') }}" class="btn btn-secondary">
                Cancelar
            </a>
        </form>
    </div>
</div>
@endsection

@push('js')
    @vite('resources/js/pages/subscription-form.js')
@endpush