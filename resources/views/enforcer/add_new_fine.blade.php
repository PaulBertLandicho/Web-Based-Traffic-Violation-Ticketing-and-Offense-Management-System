@extends('layouts.layout')
@section('title', 'Issue Driver | traffic Enforcers')

@section('content')

@include('layouts.components.enforcer.topNav')
@include('layouts.components.enforcer.leftsideNavbar')
@include('layouts.components.footer')

<!-- Dashboard main content start here -->
<div class="dashwrapper animated fadeIn">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
        <div class="container-fluid pt-4">
            <h1 class="mt-3">Issue Violations</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('enforcer.enforcer-dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Issue Traffic Violations</li>
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
                    <form id="issueFineForm" method="POST" action="{{ route('fine.store') }}">
                        @csrf
                        <input type="hidden" name="license_id" value="{{ $driver->license_id }}">

                        <h3 class="mt-4">Driver Details</h3>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="license_id">Licence ID</label>
                                <input type="text" class="form-control" id="license_id" value="{{ $driver->license_id }}" name="license_id" placeholder="Licence ID" disabled>
                                <input type="hidden" class="form-control" id="license_id" value="{{ $driver->license_id }}" name="license_id" placeholder="Licence ID">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="driver_name">Driver Full Name</label>
                                <input type="text" class="form-control" id="driver_name" value="{{ $driver->driver_name }}" name="driver_name" placeholder="Driver Full Name" disabled>
                                <input type="hidden" class="form-control" id="driver_name" value="{{ $driver->driver_name }}" name="driver_name" placeholder="Driver Full Name">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="home_address">Driver Address</label>
                                <input type="text" class="form-control" id="home_address" value="{{ $driver->home_address }}" name="home_address" placeholder="Driver Address" disabled>
                                <input type="hidden" class="form-control" id="home_address" value="{{ $driver->home_address }}" name="home_address" placeholder="Driver Address">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="license_type">License Type</label>
                                <input type="text" class="form-control" id="license_type" value="{{ $driver->license_type }}" name="license_type" placeholder="Example: A1, A, B1, B, C1, C,...etc" disabled>
                                <input type="hidden" class="form-control" id="license_type" value="{{ $driver->license_type }}" name="license_type" placeholder="Example: A1, A, B1, B, C1, C,...etc">
                            </div>
                        </div>


                        <h3 class="mt-4">Enforcer Officer Details</h3>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="enforcer_id">Enforcer Officer ID</label>
                                <input type="text" class="form-control" value="{{ $officer['enforcer_id'] ?? '' }}" id="enforcer_id" name="enforcer_id" placeholder="Enforcer Officer ID" disabled>
                                <input type="hidden" class="form-control" value="{{ $officer['enforcer_id'] ?? '' }}" id="enforcer_id" name="enforcer_id" placeholder="Enforcer Officer ID">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="enforcer_name">Enforcer Officer Name</label>
                                <input type="text" class="form-control" value="{{ $officer['enforcer_name'] ?? '' }}" id="enforcer_name" name="enforcer_name" placeholder="Enforcer Officer Name" disabled>
                                <input type="hidden" class="form-control" value="{{ $officer['enforcer_name'] ?? '' }}" id="enforcer_name" name="enforcer_name" placeholder="Enforcer Officer Name">
                            </div>
                        </div>
                        <h3 class="mt-4">Dates & Time</h3>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="issue_date">Issue Dates & Time</label>
                                <div class="d-flex">
                                    <?php
                                    // Set timezone to Philippines
                                    date_default_timezone_set('Asia/Manila');
                                    $current_date = date('Y-m-d');
                                    $current_time = date('h:i:s A');
                                    ?>
                                    <!-- Display Date -->
                                    <input type="text" class="form-control" id="issue_date" value="{{ date('Y-m-d') }}" name="issued_date" placeholder="Issue Date" disabled>
                                    <input type="hidden" class="form-control" id="issue_date_hidden" value="{{ date('Y-m-d') }}" name="issued_date" placeholder="Issue Date">

                                    &nbsp;&nbsp;

                                    <!-- Display Time -->
                                    <input type="text" class="form-control" id="issue_time" value="{{ date('h:i:s A') }}" name="issued_time" placeholder="Issue Time" disabled>
                                    <input type="hidden" class="form-control" id="issue_time_hidden" value="{{ date('h:i:s A') }}" name="issued_time" placeholder="Issue Time">
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="expire_date">Expire Date</label>
                                <input type="date" class="form-control" id="expire_date" value="{{ date('Y-m-d', strtotime('+21 days')) }}" name="expire_date" placeholder="Expire Date" disabled>
                                <input type="hidden" class="form-control" id="expire_date" value="{{ date('Y-m-d', strtotime('+21 days')) }}" name="expire_date" placeholder="Expire Date">
                            </div>
                        </div>
                        <h3 class="mt-5">Vehicle Information</h3>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="vehicle_make">Vehicle Make</label>
                                <input type="text" class="form-control" id="vehicle_make" name="vehicle_make" placeholder="e.g., Toyota">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="vehicle_model">Vehicle Model</label>
                                <input type="text" class="form-control" id="vehicle_model" name="vehicle_model" placeholder="e.g., Corolla">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="vehicle_color">Vehicle Color</label>
                                <input type="text" class="form-control" id="vehicle_color" name="vehicle_color" placeholder="e.g., Red">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="vehicle_type">Vehicle Type</label>
                                <select name="vehicle_type" id="vehicle_type" class="form-control" required>
                                    <option value="">Select Vehicle Type</option>
                                    <option value="MC">MC – Motorcycle</option>
                                    <option value="TRI">TRI – Tricycle</option>
                                    <option value="PVT">PVT – Private Vehicle</option>
                                    <option value="MV">MV – Motor Vehicle</option>
                                    <option value="PUJ">PUJ – Public Utility Jeepney</option>
                                </select>
                            </div>
                        </div>
                        <h4 class="mt-4">Violation Details</h4>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="place">Issued Place</label>
                                <select name="place" id="place" class="form-control" required>
                                    <option value="">Select Place</option>
                                    @foreach ([
                                    'Abuno', 'Acmac', 'Bagong Silang', 'Bonbonon', 'Bunawan', 'Buru-un', 'Dalipuga',
                                    'Del Carmen', 'Digkilaan', 'Ditucalan', 'Dulag', 'Hinaplanon', 'Hindang',
                                    'Kabacsanan', 'Kalilangan', 'Kiwalan', 'Lanipao', 'Luinab', 'Mahayahay',
                                    'Mainit', 'Mandulog', 'Maria Cristina', 'Pala-o', 'Panoroganan', 'Poblacion',
                                    'Puga-an', 'Rogongon', 'San Miguel', 'San Roque', 'Santa Elena',
                                    'Santa Filomena', 'Santiago', 'Santo Rosario', 'Saray-Tibanga', 'Suarez',
                                    'Tambacan', 'Tibanga', 'Tipanoy', 'Tominobo Proper',
                                    'Tominobo Upper', 'Tubod', 'Ubaldo Laya', 'Upper Hinaplanon', 'Villa Verde'
                                    ] as $barangay)
                                    <option value="{{ $barangay }}" {{ old('place') === $barangay ? 'selected' : '' }}>
                                        {{ $barangay }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="vehicle_no">Vehicle No</label>
                                <input type="text"
                                    class="form-control"
                                    id="vehicle_no"
                                    name="vehicle_no"
                                    placeholder="ABC-123 or ABC-1234"
                                    maxlength="8"
                                    required>
                            </div>
                        </div>
                        <div class="form-row">
                            <!-- Select violations -->
                            <div class="form-group col-md-6">
                                <label for="violation_type">Violation Type</label>
                                <select class="form-control" id="violation_type">
                                    <option value="" disabled selected>Select Traffic Violations</option>
                                    @foreach($violation_type as $violation)
                                    <option value="{{ $violation->violation_type }}" data-amount="{{ $violation->violation_amount }}">
                                        <!-- {{ $violation->violation_id }} - -->
                                        {{ $violation->violation_type }}
                                    </option>
                                    @endforeach
                                </select>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-sm btn-success mt-2" onclick="addViolation()"><i class="fas fa-circle-plus"></i> Add Violation</button>
                                </div>
                            </div>
                            <!-- Total Violation Amount -->
                            <div class="form-group col-md-6">
                                <label for="display_amount">Total Violation Amount (₱)</label>
                                <input type="text" class="form-control" id="display_amount" placeholder="₱0.00" readonly>
                                <input type="hidden" class="form-control" id="totalamount" name="total_amount">
                            </div>
                        </div>
                        <!-- Selected violations List -->
                        <div id="selected-violations" style="border: 1px solid #ccc; padding: 10px; max-height: 200px; overflow-y: auto; margin-bottom: 15px;"></div>
                        <input type="hidden" id="violations" name="violations_type">

                        <!-- Issue Fine Button -->
                        <button type="submit" class="btn btn-primary" id="issueFineBtn">
                            <i class="fas fa-ticket-alt" id="issueIcon"></i> Issue Fine
                        </button>
                    </form>
                </div>
            </div>

            <!-- {{--  Show Ticket Modal --}} -->


            <div class="modal fade show"
                id="ticketModal"
                tabindex="-1"
                role="dialog"
                aria-modal="true"
                style="background-color: rgba(0,0,0,0.5); overflow-y: auto; margin-top: 65px"
                data-backdrop="static"
                data-keyboard="false">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content" id="ticketModalContainer">

                    </div>
                </div>
            </div>


            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

            <script>
                function sendSMS(licenseId) {
                    fetch(`/send-sms/${licenseId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.sent_status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'SMS Sent!',
                                    html: `<p>${data.message}</p>
                               <a href="${data.view_url}" target="_blank" class="btn btn-link">📄 View Ticket</a>`,
                                    confirmButtonText: 'OK',
                                    confirmButtonColor: '#28a745'
                                });
                            } else if (data.sent_status === 'failed') {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Failed to Send',
                                    text: data.message,
                                    confirmButtonText: 'Close',
                                    confirmButtonColor: '#ffc107'
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: data.message,
                                    confirmButtonText: 'Close',
                                    confirmButtonColor: '#dc3545'
                                });
                            }
                        })
                        .catch(error => {
                            console.error(error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Connection Error',
                                text: 'Could not connect to server.',
                                confirmButtonColor: '#dc3545'
                            });
                        });
                }

                // ✅ Event delegation: works for dynamically injected modals
                document.addEventListener("click", function(e) {
                    const smsButton = e.target.closest("#send-sms");
                    if (smsButton) {
                        const licenseId = smsButton.getAttribute("data-license-id");
                        sendSMS(licenseId);
                    }
                });
            </script>

            <script>
                let selectedViolations = [];

                function addViolation() {
                    const select = document.getElementById("violation_type");
                    const selectedOption = select.options[select.selectedIndex];
                    const violationId = selectedOption.value;
                    const violationText = selectedOption.text;
                    const amount = parseFloat(selectedOption.dataset.amount);

                    // Prevent duplicates
                    if (!violationId || selectedViolations.find(v => v.text === violationText)) {
                        return;
                    }

                    selectedViolations.push({
                        id: violationId,
                        text: violationText,
                        amount: amount
                    });

                    updateViolationList();
                }

                function updateViolationList() {
                    const list = document.getElementById("selected-violations");
                    list.innerHTML = "";

                    let total = 0;

                    selectedViolations.forEach((v, i) => {
                        total += v.amount;

                        const div = document.createElement("div");
                        div.className = "alert alert-info d-flex justify-content-between align-items-center mb-2";
                        div.innerHTML = `
<span>${v.text} - ₱${v.amount.toFixed(2)}</span>
<button type="button" class="btn btn-danger btn-sm" onclick="removeViolation(${i})">X</button>
`;
                        list.appendChild(div);
                    });

                    document.getElementById("display_amount").value = "₱" + total.toFixed(2);
                    document.getElementById("totalamount").value = total.toFixed(2);
                    document.getElementById("violations").value = selectedViolations.map(v => v.text).join(", ");
                }

                function removeViolation(index) {
                    selectedViolations.splice(index, 1);
                    updateViolationList();
                }
            </script>

            <script>
                // Start Printable Citation Ticket
                function printFineDetails() {
                    const fineContent = document.getElementById("fine_detail").innerHTML;

                    const logoUrl = window.location.origin + "/assets/img/ICTPMO-logo.png";

                    const headerContent = `
<div style="text-align: center;">
    <img src="${logoUrl}" style="width: 60px; height: 60px;">
    <div style="font-size: 16px; font-weight: bold;">ICTPMO</div>
    <div style="font-size: 14px;">Traffic Violation Citation Ticket</div>
    <hr style="border-top: 1px dashed #000;">
</div>
`;

                    const footerContent = `
<hr style="border-top: 1px dashed #000;">
<div style="font-size: 11px; text-align: center;">
    This citation is not an admission of guilt.<br>
    Please retain this receipt for your records.
</div>
<br>
<div style="display: flex; justify-content: space-around; font-size: 12px;">
    <div style="text-align: center;">
        _______________________<br>
        Driver Signature
    </div>
    <div style="text-align: center;">
        _______________________<br>
        Officer Signature
    </div>
</div>
`;

                    const printWindow = window.open('', '', 'width=350,height=600'); // Small window for mobile print

                    printWindow.document.write('<html><head><title>Print Citation</title>');
                    printWindow.document.write(`
    <style>
        @media print {
            @page { margin: 5mm; size: auto; }
            body {
                font-family: monospace, sans-serif;
                font-size: 12px;
                line-height: 1.4;
                margin: 0;
                padding: 0;
                width: 100%;
            }
            table {
                width: 100%;
                border-collapse: collapse;
            }
            td {
                padding: 3px 0;
                vertical-align: top;
            }
            hr {
                margin: 6px 0;
            }
        }
    </style>
`);
                    printWindow.document.write('</head><body>');
                    printWindow.document.write(headerContent);
                    printWindow.document.write('<div style="padding: 5px;">' + fineContent + '</div>');
                    printWindow.document.write(footerContent);
                    printWindow.document.write('</body></html>');

                    printWindow.document.close();
                    printWindow.focus();

                    // Trigger print after content is loaded
                    printWindow.onload = function() {
                        printWindow.print();
                        printWindow.close();
                    };
                }
            </script>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    const issueFineForm = document.getElementById("issueFineForm");
                    const issueFineBtn = document.getElementById("issueFineBtn");

                    if (!issueFineForm || !issueFineBtn) return; // nothing to do

                    issueFineForm.addEventListener("submit", async function(e) {
                        e.preventDefault();

                        // UX: disable button and show spinner
                        const originalHtml = issueFineBtn.innerHTML;
                        issueFineBtn.disabled = true;
                        issueFineBtn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> Generating Ticket...`;

                        try {
                            // CSRF token: prefer meta, fallback to hidden _token input
                            let csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                            if (!csrf) {
                                csrf = issueFineForm.querySelector('input[name="_token"]')?.value || '';
                            }

                            const formData = new FormData(issueFineForm);

                            const response = await fetch(issueFineForm.action, {
                                method: "POST",
                                credentials: "same-origin",
                                body: formData,
                                headers: {
                                    "X-Requested-With": "XMLHttpRequest",
                                    "X-CSRF-TOKEN": csrf
                                }
                            });

                            // Helpful debug: if the server redirected (session expired/login), show message
                            if (response.redirected) {
                                console.warn("Request was redirected to:", response.url);
                            }

                            // Handle non-OK responses explicitly
                            if (!response.ok) {
                                const contentType = response.headers.get("content-type") || "";

                                // Validation errors (422) - Laravel returns JSON
                                if (response.status === 422 && contentType.includes("application/json")) {
                                    const data = await response.json();
                                    const msgs = [];
                                    if (data.errors) {
                                        for (const k in data.errors) msgs.push(...data.errors[k]);
                                    } else if (data.message) {
                                        msgs.push(data.message);
                                    }
                                    alert("Validation error:\n" + msgs.join("\n"));
                                    console.error("Validation errors:", data);
                                    return;
                                }

                                // Other JSON error (maybe controller throw)
                                if (contentType.includes("application/json")) {
                                    const json = await response.json();
                                    console.error("Server JSON error:", json);
                                    alert("Server error: " + (json.error || json.message || "see console"));
                                    return;
                                }

                                // Fallback: read text (useful when server rendered a login page or 500 trace)
                                const text = await response.text();
                                console.error("Server returned non-OK status:", response.status, text);
                                alert("Server error " + response.status + ". See console for details.");
                                return;
                            }

                            // OK -> expect HTML partial
                            const html = await response.text();

                            // Ensure the modal wrapper + container exist (create if needed)
                            if (!document.getElementById("ticketModal")) {
                                const wrapper = document.createElement("div");
                                wrapper.innerHTML = `
<div class="modal fade" id="ticketModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content" id="ticketModalContainer"></div>
  </div>
</div>`;
                                document.body.appendChild(wrapper);
                            }

                            // Check container exists now
                            const container = document.getElementById("ticketModalContainer");
                            if (!container) {
                                console.error("ticketModalContainer not found after creating wrapper.");
                                alert("Internal error: modal container missing. See console.");
                                return;
                            }

                            // Inject the returned partial (must be only modal header/body/footer)
                            container.innerHTML = html;

                            // Show the modal using bootstrap/jQuery
                            if (window.jQuery && typeof jQuery('#ticketModal').modal === 'function') {
                                jQuery('#ticketModal').modal('show');
                            } else {
                                // fallback: make it visible if bootstrap not loaded
                                document.getElementById('ticketModal').style.display = 'block';
                            }

                        } catch (err) {
                            // network error or JS error
                            console.error("AJAX error while generating ticket:", err);
                            alert("Failed to generate ticket. See browser console for details.");
                        } finally {
                            // restore button
                            issueFineBtn.disabled = false;
                            issueFineBtn.innerHTML = originalHtml;
                        }
                    });
                });

                document.addEventListener("DOMContentLoaded", function() {
                    const vehicleInput = document.getElementById("vehicle_no");

                    // Create error message element
                    const vehicleError = document.createElement("div");
                    vehicleError.id = "vehicle-error";
                    vehicleError.className = "text-danger mt-1";
                    vehicleError.style.display = "none"; // hidden by default
                    vehicleError.innerHTML = "<i class='fas fa-exclamation-circle'></i> Invalid Vehicle No format. Use ABC-123 or ABC-1234.";
                    vehicleInput.parentNode.appendChild(vehicleError);

                    vehicleInput.addEventListener("input", function(e) {
                        let value = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, "");
                        let formatted = "";

                        if (value.length > 3) {
                            formatted += value.substring(0, 3) + "-";
                            formatted += value.substring(3, 7); // allow 3 or 4 digits
                        } else {
                            formatted = value;
                        }

                        e.target.value = formatted;

                        // Regex: ABC-123 or ABC-1234
                        const vehiclePattern = /^[A-Z]{3}-\d{3,4}$/;

                        if (formatted.length > 0 && !vehiclePattern.test(formatted)) {
                            vehicleError.style.display = "block"; // show error
                            vehicleInput.classList.add("is-invalid");
                        } else {
                            vehicleError.style.display = "none"; // hide error
                            vehicleInput.classList.remove("is-invalid");
                        }
                    });
                });
            </script>

            @endsection