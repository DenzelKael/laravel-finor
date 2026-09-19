@extends('adminlte::page')

@section('title', $plan->exists ? 'Editar Plan' : 'Nuevo Plan')

@section('content_header')
    <h1>{{ $plan->exists ? 'Editar Plan' : 'Nuevo Plan' }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ $plan->exists ? route('plans.update', $plan) : route('plans.store') }}" method="POST">
                @csrf
                @if ($plan->exists)
                    @method('PUT')
                @endif

                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" name="nombre" id="nombre"
                           class="form-control @error('nombre') is-invalid @enderror"
                           value="{{ old('nombre', $plan->nombre) }}">
                    @error('nombre')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea name="descripcion" id="descripcion" rows="3"
                              class="form-control @error('descripcion') is-invalid @enderror">{{ old('descripcion', $plan->descripcion) }}</textarea>
                    @error('descripcion')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="precio">Precio</label>
                    <input type="number" step="0.01" name="precio" id="precio"
                           class="form-control @error('precio') is-invalid @enderror"
                           value="{{ old('precio', $plan->precio) }}">
                    @error('precio')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="duracion_dias">Duración (días)</label>
                    <input type="number" name="duracion_dias" id="duracion_dias"
                           class="form-control @error('duracion_dias') is-invalid @enderror"
                           value="{{ old('duracion_dias', $plan->duracion_dias) }}">
                    @error('duracion_dias')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group form-check">
                    <input type="hidden" name="activo" value="0">
                    <input type="checkbox" name="activo" id="activo" value="1" class="form-check-input"
                           {{ old('activo', $plan->activo) ? 'checked' : '' }}>
                    <label for="activo" class="form-check-label">Activo</label>
                </div>

                <button type="submit" class="btn btn-primary">Guardar</button>
                <a href="{{ route('plans.index') }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
@stop