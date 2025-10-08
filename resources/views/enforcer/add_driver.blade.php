@extends('layouts.layout')
@section('title', 'Retrieve Driver Info | traffic Enforcers')

@section('content')
@include('layouts.components.enforcer.topNav')
@include('layouts.components.enforcer.leftsideNavbar')
@include('layouts.components.footer')

<!-- Dashboard main content start here -->
<div class="dashwrapper animated fadeIn">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
        <div class="container-fluid pt-4">
            <h1 class="mt-3">Driver information</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('enforcer.enforcer-dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Retrieve Driver info</li>
            </ol>

            {{-- Laravel Error and Success Messages --}}
            @if(session('success'))
            <div class="alert alert-success" id="success-alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger" id="success-alert">
                <i class="fas fa-exclamation-circle"></i>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
            @endif
            <div class="card mb-4">
                <div class="card-body p-lg-5">
                    <form action="{{ route('drivers.store') }}" method="POST">
                        @csrf
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="license_id_input">License ID</label>
                                <input type="text"
                                    class="form-control"
                                    id="license_id_input"
                                    name="licenseid"
                                    placeholder="License ID"
                                    maxlength="13"
                                    required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="license_type">License Type</label>
                                <select name="licensetype" class="form-control" id="license_type">
                                    <option value="">Select</option>
                                    <option value="Professional Licenses">Professional Licenses</option>
                                    <option value="Non-Professional Licenses">Non-Professional Licenses</option>
                                    <option value="Student Permits">Student Permits</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="driver_name">Driver Full Name</label>
                                <input type="text"
                                    class="form-control"
                                    id="driver_name"
                                    name="drivername"
                                    placeholder="Driver Full Name"
                                    value="{{ old('drivername') }}">
                            </div>
                            <div class="form-group col-md-6 position-relative">
                                <label for="date_of_birth">Date of Birth</label>
                                <input type="date"
                                    class="form-control pr-5 custom-date-input"
                                    id="date_of_birth"
                                    name="dateofbirth"
                                    value="{{ old('dateofbirth') }}">
                                <span class="calendar-icon" onclick="document.getElementById('date_of_birth').showPicker()">
                                    <i class="fas fa-calendar-alt"></i>
                                </span>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="home_address">Home Address</label>

                                <input type="text"
                                    class="form-control"
                                    id="home_address"
                                    name="homeaddress"
                                    placeholder="Driver Address"
                                    value="{{ old('homeaddress') }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="contact_no">Contact No.</label>
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
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="license_issue_date">License Issue Date</label>
                                    <input type="date"
                                        class="form-control custom-date-input"
                                        id="license_issue_date"
                                        name="licenseissuedate"
                                        value="{{ old('licenseissuedate') }}">
                                    <span class="calendar-icon" onclick="document.getElementById('license_issue_date').showPicker()">
                                        <i class="fas fa-calendar-alt"></i>
                                    </span>
                                </div>
                                <div class="form-group col-md-6 position-relative">
                                    <label for="license_expire_date">License Expire Date</label>
                                    <input type="date"
                                        class="form-control custom-date-input"
                                        id="license_expire_date"
                                        name="licenseexpiredate"
                                        value="{{ old('licenseexpiredate') }}">
                                    <span class="calendar-icon" onclick="document.getElementById('license_expire_date').showPicker()">
                                        <i class="fas fa-calendar-alt"></i>
                                    </span>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Proceed <i class="fas fa-arrow-right"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Dashboard main content end here ========================================-->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const licenseInput = document.getElementById("license_id_input");

        // Create error message element
        const licenseError = document.createElement("div");
        licenseError.id = "license-error";
        licenseError.className = "text-danger mt-1";
        licenseError.style.display = "none"; // hidden by default
        licenseError.innerHTML = "<i class='fas fa-exclamation-circle'></i> Invalid License ID format. Use AB-12-345678 or N01-23-456789.";
        licenseInput.parentNode.appendChild(licenseError);

        licenseInput.addEventListener("input", function(e) {
            let value = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, "");
            let formatted = "";

            // OLD FORMAT -> AB-12-345678
            if (/^[A-Z]{2}/.test(value)) {
                if (value.length > 2) {
                    formatted += value.substring(0, 2) + "-";
                    if (value.length > 4) {
                        formatted += value.substring(2, 4) + "-";
                        formatted += value.substring(4, 10);
                    } else {
                        formatted += value.substring(2);
                    }
                } else {
                    formatted = value;
                }
            } else {
                // NEW FORMAT -> N01-23-456789
                if (value.length > 3) {
                    formatted += value.substring(0, 3) + "-";
                    if (value.length > 5) {
                        formatted += value.substring(3, 5) + "-";
                        formatted += value.substring(5, 11);
                    } else {
                        formatted += value.substring(3);
                    }
                } else {
                    formatted = value;
                }
            }

            e.target.value = formatted;

            // Regex: AB-12-345678 or N01-23-456789
            const licensePattern = /^([A-Z]{2}-\d{2}-\d{6}|[A-Z]\d{2}-\d{2}-\d{6})$/;

            if (formatted.length > 0 && !licensePattern.test(formatted)) {
                licenseError.style.display = "block"; // show error
                licenseInput.classList.add("is-invalid");
            } else {
                licenseError.style.display = "none"; // hide error
                licenseInput.classList.remove("is-invalid");
            }
        });
    });

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