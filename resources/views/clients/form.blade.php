<div class="form-group">
    <label for="name">Nombre</label>
    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
           value="{{ old('name', $client->name ?? '') }}">
    @error('name')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    <label for="email">Email</label>
    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
           value="{{ old('email', $client->email ?? '') }}">
    @error('email')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    <label for="phone">Telefono</label>
    <input type="text" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror"
           value="{{ old('phone', $client->phone ?? '') }}">
    @error('phone')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    <label for="address">Direccion</label>
    <input type="text" id="address" name="address" class="form-control @error('address') is-invalid @enderror"
           value="{{ old('address', $client->address ?? '') }}">
    @error('address')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>
