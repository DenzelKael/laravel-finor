@extends('adminlte::page')

@section('title', 'Nueva Suscripción')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Nueva Suscripción</h1>
        <a href="{{ route('subscriptions.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left mr-1"></i> Volver al Listado
        </a>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">Formulario de Alta de Suscripción</h3>
            </div>

            <form id="subscription-form"
                  data-store-url="{{ route('subscriptions.store') }}"
                  data-index-url="{{ route('subscriptions.index') }}">
                @csrf

                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="client_id" class="form-label font-weight-bold">
                            Cliente <span class="text-danger">*</span>
                        </label>
                        <select name="client_id" id="client_id" class="form-control" required>
                            <option value="">-- Seleccione un cliente --</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}" @selected(old('client_id') == $client->id)>
                                    {{ $client->name }} ({{ $client->email }})
                                </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="plan_id" class="form-label font-weight-bold">
                            Plan Activo <span class="text-danger">*</span>
                        </label>
                        <select name="plan_id" id="plan_id" class="form-control" required>
                            <option value="">-- Seleccione un plan activo --</option>
                            @foreach ($plans as $plan)
                                <option value="{{ $plan->id }}" @selected(old('plan_id') == $plan->id)>
                                    {{ $plan->nombre }} - {{ number_format($plan->precio, 2) }} Bs ({{ $plan->duracion_dias }} días)
                                </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="start_date" class="form-label font-weight-bold">
                            Fecha de Inicio
                        </label>
                        <input type="date"
                               name="start_date"
                               id="start_date"
                               class="form-control"
                               value="{{ old('start_date', date('Y-m-d')) }}">
                        <small class="form-text text-muted">Si no se especifica, se tomará la fecha actual.</small>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-end gap-2">
                    <a href="{{ route('subscriptions.index') }}" class="btn btn-secondary me-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Guardar Suscripción
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
    @vite(['resources/js/subscriptions/create.js'])
@endsection
