@extends('admin.layouts.layout')

@section('title', 'Verify Code')

@section('content')
<div class="container mt-5">
    <h2>Enter Verification Code</h2>

    @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger">
        {{ $errors->first() }}
    </div>
    @endif

    <form method="POST" action="{{ route('admin.verification.code.check') }}">
        @csrf
        <div class="mb-3">
            <label for="code">Verification Code:</label>
            <input type="text" name="code" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Verify Code</button>
    </form>
</div>
@endsection