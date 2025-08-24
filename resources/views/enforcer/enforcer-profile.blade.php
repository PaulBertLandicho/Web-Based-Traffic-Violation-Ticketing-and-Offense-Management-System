@extends('layouts.layout')
@section('title', 'Edit Profile | traffic Enforcers')

@section('content')
@include('layouts.components.enforcer.topNav')
@include('layouts.components.enforcer.leftsideNavbar')

<div class="dashwrapper animated fadeIn">
    <div class="container-fluid pt-4">
        <h1 class="mt-3">Edit Profile</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('enforcer.enforcer-dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Edit Profile</li>
        </ol>

        <!-- Flash messages -->
        @if(session('error'))
        <div class="alert alert-danger" id="success-alert">{{ session('error') }}</div>
        @endif
        @if(session('success'))
        <div class="alert alert-success" id="success-alert">{{ session('success') }}</div>
        @endif

        <!-- Validation errors -->
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="card mb-4">
            <div class="card-body p-lg-5">

                <!-- Email Display -->
                <h4>Your Email Address</h4>
                <div class="form-group">
                    <input type="email" class="form-control" value="{{ session('enforcer_email') }}" disabled>
                </div>

                <!-- Change Password Form -->
                <form method="POST" action="{{ route('enforcer.profile.updatePassword') }}">
                    @csrf
                    <h4 class="mt-4">Change Your Password</h4>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Old Password</label>
                            <input type="password" name="oldpassword" class="form-control" id="oldPassword" placeholder="Old Password" required>
                            <span class="toggle-password" onclick="togglePassword(this)">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                        <div class="form-group col-md-4">
                            <label>New Password</label>
                            <input type="password" name="newpassword" class="form-control" id="newPassword" placeholder="New Password" required>
                            <span class="toggle-password" onclick="togglePassword(this)">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Confirm New Password</label>
                            <input type="password" name="passwordconfirm" class="form-control" id="confirmPassword" placeholder="Confirm New Password" required>
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
</script>

@endsection