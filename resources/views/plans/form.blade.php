<div class="form-group">
    <label>Nombre</label>
    <input type="text" name="nombre" class="form-control"
        value="{{ old('nombre', $plan->nombre ?? '') }}">
    <span class="invalid-feedback"></span>
</div>

<div class="form-group">
    <label>Descripción</label>
    <textarea name="descripcion" rows="3" class="form-control">{{ old('descripcion', $plan->descripcion ?? '') }}</textarea>
    <span class="invalid-feedback"></span>
</div>

<div class="form-group">
    <label>Precio</label>
    <input type="number" step="0.01" name="precio" class="form-control"
        value="{{ old('precio', $plan->precio ?? '') }}">
    <span class="invalid-feedback"></span>
</div>

<div class="form-group">
    <label>Duración (días)</label>
    <input type="number" name="duracion_dias" class="form-control"
        value="{{ old('duracion_dias', $plan->duracion_dias ?? '') }}">
    <span class="invalid-feedback"></span>
</div>

<div class="form-group form-check">
    <input type="hidden" name="activo" value="0">
    <input type="checkbox" name="activo" value="1" class="form-check-input"
        {{ old('activo', $plan->activo ?? false) ? 'checked' : '' }}>
    <label class="form-check-label">Activo</label>
    <span class="invalid-feedback"></span>
</div>
