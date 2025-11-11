@extends('layouts.layout')
@section('title', 'Forgot Password | ICTPMO')

@section('content')
@section('css', 'assets/css/login.css')

<div class="hero_area">
    <div class="container">
        <div class="row login-section">
            <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
                <div class="card card-signin my-5">
                    <div class="card-body animated fadeIn">
                        <h1 class="card-icon"><i class="fas fa-unlock-alt"></i></h1>
                        <h5 class="card-title text-center">Admin Forgot Password</h5>

                        @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <form class="form-signin" action="{{ route('admin.forgot.send') }}" method="POST">
                            @csrf
                            <div class="form-label-group">
                                <input type="email" name="email" id="inputEmail" class="form-control" placeholder="admin@example.com" required value="{{ old('email') }}">
                            </div>
                            <button class="btn btn-lg btn-block text-uppercase" type="submit">Continue</button>
                            <hr class="my-4">
                            <h6 style="text-align: center;">
                                <a href="admin-login"><i class="fas fa-sign-in-alt"></i> Back to Login</a>
                                <span class="ml-2"><a href="{{ url('/admin-login') }}"><i class="fas fa-home"></i> Home</a></span>
                            </h6>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection