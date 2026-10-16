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

<div class="form-group">
    <label for="status">Estado</label>
    <select id="status" name="status" class="form-control @error('status') is-invalid @enderror">
        @foreach (\App\Enums\ClientStatus::cases() as $status)
            <option value="{{ $status->value }}"
                @selected(old('status', isset($client) ? $client->status->value : 'active') === $status->value)>
                {{ $status->label() }}
            </option>
        @endforeach
    </select>
    @error('status')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>