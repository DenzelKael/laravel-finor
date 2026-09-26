@extends('adminlte::page')

@section('title', 'New Client')

@section('content_header')
    <h1>New Client</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('clients.store') }}" method="POST">
            @csrf
            @include('clients.form')

            <button type="submit" class="btn btn-primary">Save</button>
            <a href="{{ route('clients.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
