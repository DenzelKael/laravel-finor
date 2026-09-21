@extends('adminlte::page')

@section('title', 'New Client')

@section('content_header')
    <h1>New Client</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form id="create-form">
            @csrf
            @include('clients.form')

            <button type="submit" class="btn btn-primary">Save</button>
            <a href="{{ route('clients.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection

@section('js')
@vite(['resources/js/app.js'])
<script>
    document.getElementById('create-form').addEventListener('submit', async function (e) {
        e.preventDefault();

        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');

        const formData = new FormData(this);

        const { status, data } = await window.api.post("{{ route('clients.store') }}", formData);

        if (status === 422) {
            for (const field in data.errors) {
                const input = document.querySelector(`[name="${field}"]`);
                if (input) {
                    input.classList.add('is-invalid');
                    input.nextElementSibling.textContent = data.errors[field][0];
                }
            }
            return;
        }

        if (data.success) {
            window.location.href = "{{ route('clients.index') }}";
        }
    });
</script>
@endsection

