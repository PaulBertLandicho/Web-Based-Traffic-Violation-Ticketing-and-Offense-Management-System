@extends('layouts.layout')

@section('title', 'Page Not Found')

@section('content')
<div class="d-flex flex-column justify-content-center align-items-center" style="height:100vh; text-align:center;">
    <img src="{{ asset('assets/img/404.svg') }}" alt="404 Not Found" style="max-width:500px; width:100%;">
    <h1 class="mt-4">Oops! Page Not Found</h1>
    <p class="text-muted">The page you are looking for doesn't exist or has been moved.</p>
    <button onclick="window.history.back()" class="btn btn-primary mt-3">⬅ Back to Previous Page</button>
</div>
@endsection