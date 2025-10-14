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
                <!-- Profile Info Form -->
                <form method="POST" action="{{ route('enforcer.profile.update') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="row align-items-center">
                        <div class="col-md-3 text-center">
                            @php
                            $imagePath = DB::table('traffic_enforcers')
                            ->where('enforcer_id', session('enforcer_id'))
                            ->value('profile_image');

                            // ✅ Show default image if none uploaded
                            $profileImage = $imagePath ? asset($imagePath) : asset('assets/img/default-enforcer.png');
                            @endphp

                            <!-- Profile Image Preview -->
                            <img id="profilePreview"
                                src="{{ $profileImage }}"
                                alt="Profile Image"
                                class="rounded-circle mb-3 shadow-sm"
                                width="130" height="130"
                                style="object-fit: cover; border: 3px solid #28a745;">

                            <!-- Upload Input -->
                            <input type="file"
                                name="profile_image"
                                id="profileImageInput"
                                class="form-control mt-2"
                                accept="image/*">
                        </div>

                        <div class="col-md-9">
                            <div class="form-group">
                                <label for="enforcer_name">Traffic Enforcer Name</label>
                                <input type="text"
                                    name="enforcer_name"
                                    class="form-control"
                                    value="{{ session('enforcer_name') }}"
                                    required>
                            </div>

                            <button type="submit" class="btn btn-success mt-2">
                                <i class="fas fa-save"></i> Update Profile
                            </button>
                        </div>
                    </div>
                </form>

                <hr class="my-4">

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

    document.getElementById('profileImageInput').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const preview = document.getElementById('profilePreview');

        if (file) {
            const reader = new FileReader();
            reader.onload = e => preview.src = e.target.result;
            reader.readAsDataURL(file);
        } else {
            // Revert to default image if no file selected
            preview.src = "{{ asset('assets/img/default-enforcer.png') }}";
        }
    });
</script>

@endsection