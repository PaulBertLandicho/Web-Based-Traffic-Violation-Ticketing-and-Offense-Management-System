@extends('layouts.layout')
@section('title', 'Add Traffic Officer | Traffic Administrative')

@section('content')
@include('layouts.components.admin.topNav')
@include('layouts.components.admin.leftsideNavbar')

<!-- Dashboard main content start here -->
<div class="dashwrapper animated fadeIn">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
        <div class="container-fluid">
            <h1 class="mt-4">Add Traffic Enforcers</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                <li class="breadcrumb-item active">Add Traffic Enforcers</li>
            </ol>

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif


            <!-- Form Card -->
            <div class="card mb-4">
                <div class="card-body p-lg-5">
                    <form action="{{ route('enforcers.store') }}" method="POST">
                        @csrf

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="enforcer_name">Traffic Enforcer Name</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="enforcer_name"
                                    name="enforcername"
                                    value="{{ old('enforcername') }}"
                                    placeholder="Traffic Enforcer Name"
                                    required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="enforcer_email">Traffic Enforcer Email</label>
                                <input type="email" class="form-control" id="enforcer_email" name="enforceremail" value="{{ old('enforceremail') }}" placeholder="Traffic Officer Email">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="enforcer_password">Traffic Enforcer Password</label>
                                <input type="password" class="form-control" id="enforcer_password" name="enforcerpassword" placeholder="Password">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="enforcer_password_confirm">Confirm Password</label>
                                <input type="password" class="form-control" id="enforcer_password_confirm" name="enforcerpasswordconfirm" placeholder="Confirm Password">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="gender">Gender</label>
                                <select
                                    name="gender"
                                    id="gender"
                                    class="form-control"
                                    required>
                                    <option value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="assignedarea">Enforcer Area</label>
                                <select
                                    name="assignedarea"
                                    id="assignedarea"
                                    class="form-control"
                                    required>
                                    <option value="">Select Assigned Area</option>
                                    @foreach ([
                                    'Abuno', 'Acmac', 'Bagong Silang', 'Bonbonon', 'Bunawan', 'Buru-un', 'Dalipuga',
                                    'Del Carmen', 'Digkilaan', 'Ditucalan', 'Dulag', 'Hinaplanon', 'Hindang',
                                    'Kabacsanan', 'Kalilangan', 'Kiwalan', 'Lanipao', 'Luinab', 'Mahayahay',
                                    'Mainit', 'Mandulog', 'Maria Cristina', 'Pala-o', 'Panoroganan', 'Poblacion',
                                    'Puga-an', 'Rogongon', 'San Miguel', 'San Roque', 'Santa Elena',
                                    'Santa Filomena', 'Santiago', 'Santo Rosario', 'Saray-Tibanga', 'Suarez',
                                    'Tambacan', 'Tibanga', 'Tipanoy', 'Tominobo Proper',
                                    'Tominobo Upper', 'Tubod', 'Ubaldo Laya', 'Upper Hinaplanon', 'Villa Verde'
                                    ] as $area)
                                    <option value="{{ $area }}" {{ old('assignedarea') === $area ? 'selected' : '' }}>
                                        {{ $area }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="contact_no">Enforcer Contact:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text d-flex align-items-center">
                                            <img src="https://flagcdn.com/w20/ph.png" alt="PH" style="width:20px; height:14px; margin-right:6px;">
                                            +63
                                        </span>
                                    </div>
                                    <input type="text"
                                        class="form-control"
                                        id="contact_no"
                                        name="contactno"
                                        placeholder="9XXXXXXXXX"
                                        maxlength="10"
                                        required
                                        value="{{ old('contactno') }}">
                                </div>
                                <div id="contact-error" class="text-danger mt-1" style="display:none;">
                                    <i class="fas fa-exclamation-circle"></i> Contact number must start with 9 and be 10 digits long.
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="registered_at">Enforcer Registered</label>
                                <input type="date" class="form-control" id="registered_at" value="{{ now()->toDateString() }}" disabled>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary" name="add-tpo-submit">Add Traffic Enforcer</button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
<!-- Dashboard main content end -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const contactInput = document.getElementById("contact_no");
        const errorMsg = document.getElementById("contact-error");

        contactInput.addEventListener("input", function() {
            const pattern = /^9\d{9}$/; // must start with 9 + 9 more digits (total 10)

            if (contactInput.value.length > 0 && !pattern.test(contactInput.value)) {
                contactInput.classList.add("is-invalid");
                errorMsg.style.display = "block";
            } else {
                contactInput.classList.remove("is-invalid");
                errorMsg.style.display = "none";
            }
        });
    });
</script>
@endsection