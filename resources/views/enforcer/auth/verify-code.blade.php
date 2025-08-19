@extends('layouts.layout')

@section('content')
<div class="container">
    <h3>Verify Code</h3>
    @if (session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif
    @if (session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    <form method="POST" action="{{ route('enforcer.verify.code') }}">
        @csrf
        <input type="text" name="code" placeholder="Enter verification code" required>
        <button type="submit">Verify</button>
    </form>
</div>
@endsection