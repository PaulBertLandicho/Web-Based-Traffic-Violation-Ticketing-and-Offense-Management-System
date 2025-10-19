@extends('layouts.layout')
@section('title', 'Admin Profile | Traffic Administrative')

@section('content')
@include('layouts.components.admin.topNav')
@include('layouts.components.admin.leftsideNavbar')
@include('layouts.components.footer')

<div class="dashwrapper animated fadeIn">
    <div class="container-fluid pt-4">
        <h1 class="mt-4">Edit Admin Profile</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('admin.admin-dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Edit Profile</li>
        </ol>

        @if(session('success'))
        <div class="alert alert-success" id="success-alert">{{ session('success') }}</div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger" id="success-alert">{{ session('error') }}</div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="card mb-4">
            <div class="card-body p-lg-5">
                <h4>Your Email Address</h4>
                <input type="email" class="form-control mb-4" value="{{ session('admin_email') }}" disabled>

                <form method="POST" action="{{ route('admin.updatePassword') }}">
                    @csrf
                    <h4>Change Your Password</h4>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Old Password</label>
                            <input type="password" name="oldpassword" class="form-control" placeholder="Old Password" required>
                            <span class="toggle-password" onclick="togglePassword(this)">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                        <div class="form-group col-md-4">
                            <label>New Password</label>
                            <input type="password" name="newpassword" class="form-control" placeholder="New Password" required>
                            <span class="toggle-password" onclick="togglePassword(this)">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Confirm New Password</label>
                            <input type="password" name="passwordconfirm" class="form-control" placeholder="Confirm New Password" required>
                            <span class="toggle-password" onclick="togglePassword(this)">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary mt-3"><i class="fas fa-save"></i> Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePassword(iconWrapper) {
        const input = iconWrapper.previousElementSibling;
        const icon = iconWrapper.querySelector("i");

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

    $("#success-alert").delay(4000).fadeTo(2000, 500).slideUp(1000, function() {
        $(this).slideUp(1000);
    });
</script>
@endsection