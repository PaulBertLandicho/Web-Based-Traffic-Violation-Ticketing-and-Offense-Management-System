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
                    <form method="POST" action="{{ route('fine.store') }}">
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
                                <input type="text" class="form-control" id="vehicle_no" name="vehicle_no" placeholder="Vehicle No">
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
                                    <button type="button" class="btn btn-sm btn-success mt-2" onclick="addViolation()">Add Violation</button>
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
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-ticket-alt"></i> Issue Fine
                        </button>

                        <!-- Cancel Button with JS function to avoid conflicts -->
                        <button type="button" class="btn btn-danger" onclick="redirectToDashboard()">
                            <i class="fas fa-times-circle"></i> Cancel
                        </button>

                    </form>
                </div>
            </div>

            <!-- {{--  Show Ticket Modal --}} -->
            @if(session('show_ticket'))
            @php

            // Get ticket record from DB
            $ticket = DB::table('issued_fine_tickets')
            ->where('ref_no', session('ref_no'))
            ->first();

            // Fallbacks if DB doesn't have the data
            $offenseNumber = $ticket->offense_number ?? 1;
            $totalAmount = $ticket->total_amount ?? 0;
            @endphp

            <div class="modal fade show" id="ticketModal" tabindex="-1" role="dialog" style="display: block; background-color: rgba(0,0,0,0.5); overflow-y: auto; margin-top: 65px">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content" id="printableTicket">
                        <div class="modal-header bg-info text-white">
                            <h4 class="modal-title" id="ticketDetailsLabel">
                                <img src="../assets/img/ICTPMO-logo.png" style="width: 40px; height: 40px; margin-right: 10px;">
                                Citation Ticket Details
                            </h4>
                            <a href="{{ route('fine.clearTicket') }}" class="btn btn-danger"><span>&times;</span></a>
                        </div>

                        <div class="modal-body" id="fine_detail">
                            <div class="ticket px-3">
                                <p class="text-center">Citation No: <strong># {{ session('ref_no') }}</strong></p>

                                <h4 class="border-bottom pb-1"><strong>Driver Information</strong></h4>
                                <table class="table table-sm table-borderless mb-2">
                                    <tr>
                                        <td>Full Name:</td>
                                        <td>{{ $driver->driver_name }}</td>
                                    </tr>
                                    <tr>
                                        <td>License Number:</td>
                                        <td>{{ $driver->license_id }}</td>
                                    </tr>
                                    <tr>
                                        <td>License Type:</td>
                                        <td>{{ session('license_type') }}</td>
                                    </tr>
                                    <tr>
                                        <td>Address:</td>
                                        <td>{{ session('home_address') }}</td>
                                    </tr>
                                </table>

                                <h4 class="border-bottom pb-1"><strong>Vehicle Information</strong></h4>
                                <table class="table table-sm table-borderless mb-2">
                                    <tr>
                                        <td>Vehicle Number:</td>
                                        <td>{{ session('vehicle_no') }}</td>
                                    </tr>
                                    <tr>
                                        <td>Vehicle Make:</td>
                                        <td>{{ session('vehicle_make') }}</td>
                                    </tr>
                                    <tr>
                                        <td>Vehicle Model:</td>
                                        <td>{{ session('vehicle_model') }}</td>
                                    </tr>
                                    <tr>
                                        <td>Vehicle Color:</td>
                                        <td>{{ session('vehicle_color') }}</td>
                                    </tr>
                                    <tr>
                                        <td>Vehicle Type:</td>
                                        <td>{{ session('vehicle_type') }}</td>
                                    </tr>
                                </table>

                                <h4 class="border-bottom pb-1"><strong>Violation Details</strong></h4>
                                <table class="table table-sm table-borderless mb-2">
                                    <tr>
                                        <td>Issued Place:</td>
                                        <td>{{ session('place') }}</td>
                                    </tr>
                                    <tr>
                                        <td>Issued Date & Time:</td>
                                        <td>{{ session('issued_date') }} {{ session('issued_time') }}</td>
                                    </tr>
                                    <tr>
                                        <td>Expiration Date:</td>
                                        <td>{{ session('expire_date') }}</td>
                                    </tr>
                                    <p><strong>Offense Number:</strong>
                                        @if($offenseNumber == 1)
                                        1st Offense
                                        @elseif($offenseNumber == 2)
                                        2nd Offense
                                        @elseif($offenseNumber == 3)
                                        3rd Offense
                                        @else
                                        {{ $offenseNumber }}th Offense
                                        @endif
                                    </p>

                                    <tr>
                                        <td>Provisions:</td>
                                        <td>
                                            @php
                                            $violationTypes = session('violation_type') ? explode(',', session('violation_type')) : [];
                                            @endphp
                                            @if(count($violationTypes) > 0)
                                            @foreach($violationTypes as $type)
                                            [ {{ trim($type) }} ]<br>
                                            @endforeach
                                            @else
                                            [ ]
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Total Amount:</td>
                                        <td><strong>₱{{ number_format($totalAmount, 2) }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td>Status:</td>
                                        <td><span class="badge badge-warning">Pending</span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" onclick="printFineDetails()">
                                <i class="fas fa-print"></i> Print
                            </button>
                            <button class="btn btn-primary" id="send-sms" data-license-id="{{ $driver->license_id }}">
                                <i class="fas fa-paper-plane"></i>Send SMS
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endif

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
                function redirectToDashboard() {
                    window.location.href = "{{ route('enforcer.enforcer-dashboard') }}";
                }
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
                function sendSMS(licenseId) {
                    fetch(`/send-sms/${licenseId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                alert(`${data.message}\n\nView: ${data.view_url}`);
                            } else {
                                alert(`Failed: ${data.message}`);
                            }
                        })
                        .catch(error => {
                            console.error(error);
                            alert('Error: Could not connect to server.');
                        });
                }

                // ✅ Attach event listener AFTER DOM is fully loaded
                document.addEventListener("DOMContentLoaded", function() {
                    const smsButton = document.getElementById("send-sms");
                    if (smsButton) {
                        smsButton.addEventListener("click", function() {
                            const licenseId = smsButton.getAttribute("data-license-id");
                            sendSMS(licenseId);
                        });
                    }
                });
            </script>

            @endsection