@extends('layouts.layout')
@section('title', 'Enforcer Login | ICTPMO')

@section('content')
@section('css', 'assets/css/login.css')

<!--Login form start here--->
<div class="container">
    <div class="row login-section">
        <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
            <div class="card card-signin my-5">
                <div class="card-body animated fadeIn">
                    <h1 class="card-icon"><i class="fas fa-book-reader"></i></h1>
                    <h5 class="card-title text-center">Traffic Enforcer Log In</h5>
                    @if (session('error'))
                    <div class="alert alert-danger" id="success-alert">
                        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    </div>
                    @endif

                    @if (session('success'))
                    <div class="alert alert-success" id="success-alert">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                    </div>
                    @endif

                    <form class="form-signin" action="{{ route('enforcer.login.submit') }}" method="POST">
                        @csrf
                        <div class="form-label-group">
                            <input type="text" id="inputID" name="enforcer_id" class="form-control" placeholder="Enforcer ID" required>
                        </div>
                        <div class="form-label-group position-relative">
                            <input type="password" id="inputPassword" name="enforcer_password" class="form-control pr-5" placeholder="Password" required>
                            <span class="toggle-password" onclick="togglePassword(this)">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>

                        <button class="btn btn-lg btn-block text-uppercase" type="submit">Log in</button>
                        <hr class="my-4">
                        <h6 style="text-align: center;">
                            <a href="enforcer-forgot-password"><i class="fas fa-unlock-alt"></i> Forgot Password?</a>
                            <span class="ml-2"><a href="{{ url('/gov') }}"><i class="fas fa-home"></i> Home</a></span>
                        </h6>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Login form end here--->

<script>
    function togglePassword(iconWrapper) {
        var input = document.getElementById("inputPassword");
        var icon = iconWrapper.querySelector("i");

        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            input.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    }
</script>

<!--===============================================================================================-->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>

@endsection