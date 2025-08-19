@extends('admin.layouts.layout')

@section('title', 'Reset Password')

@section('content')
<div class="container mt-5">
    <h2>Reset Your Password</h2>

    @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger">
        {{ $errors->first() }}
    </div>
    @endif

    <form method="POST" action="{{ route('admin.reset.password.submit') }}">
        @csrf
        <div class="mb-3">
            <label for="password">New Password:</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="password_confirmation">Confirm Password:</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Reset Password</button>
    </form>
</div>
@endsection