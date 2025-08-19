@extends('layouts.layout')

@section('content')
<div class="container">
    <h3>Reset Password</h3>
    <form method="POST" action="{{ route('enforcer.reset.password') }}">
        @csrf
        <input type="password" name="password" placeholder="New password" required>
        <input type="password" name="password_confirmation" placeholder="Confirm password" required>
        <button type="submit">Reset Password</button>
    </form>
</div>
@endsection