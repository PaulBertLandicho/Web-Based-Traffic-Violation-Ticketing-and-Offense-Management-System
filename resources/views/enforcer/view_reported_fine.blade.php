@extends('layouts.layout')
@section('title', 'View Reported Fine | traffic Enforcers')

@section('content')
@include('layouts.components.enforcer.topNav')
@include('layouts.components.enforcer.leftsideNavbar')

<!-- Dashboard main content start here -->
<div class="dashwrapper animated fadeIn">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
        <div class="container-fluid pt-4">
            <h1 class="mt-3">View Reported Fine</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('enforcer.enforcer-dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">View Reported Fine</li>
            </ol>

            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table mr-1"></i> You can sort data here
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dataTable" class="table table-striped table-bordered" width="100%">
                            <thead>
                                <tr>
                                    <th>Reference No</th>
                                    <th>License ID</th>
                                    <th>Violations</th>
                                    <th>Vehicle No</th>
                                    <th>Total Amount</th>
                                    <th>Issued Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($fines as $fine)
                                <tr>
                                    <td>{{ $fine->ref_no }}</td>
                                    <td>{{ $fine->license_id }}</td>
                                    <td>{{ $fine->violation_type }}</td>
                                    <td>{{ $fine->vehicle_no }}</td>
                                    <td>₱{{ number_format($fine->total_amount, 2) }}</td>
                                    <td>{{ $fine->issued_date }}</td>
                                </tr>
                                @endforeach
                                @if(count($fines) === 0)
                                <tr>
                                    <td colspan="6" class="text-center">No fines issued yet.</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/vendors/jquery/jquery-3.5.1.js') }}"></script>
    <script src="{{ asset('assets/vendors/bootstrap/popper.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/bootstrap/bootstrap.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- ✅ DataTables & Export Buttons -->
    <script src="{{ asset('assets/vendors/DataTables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/DataTables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/DataTables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/DataTables/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/DataTables/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/DataTables/vfs_fonts.js') }}"></script>
    <script src="{{ asset('assets/vendors/DataTables/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/DataTables/buttons.print.min.js') }}"></script>
    <script>
        $('#dataTable').DataTable({
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'csv',
                    className: 'btn btn-primary mb-3',
                    exportOptions: {
                        columns: ':not(:first-child)' // skip "Action" column
                    }
                },
                {
                    extend: 'excel',
                    className: 'btn btn-success mb-3',
                    exportOptions: {
                        columns: ':not(:first-child)'
                    }
                },
                {
                    extend: 'pdf',
                    className: 'btn btn-danger mb-3',
                    exportOptions: {
                        columns: ':not(:first-child)'
                    }
                },
                {
                    extend: 'print',
                    className: 'btn btn-dark mb-3',
                    exportOptions: {
                        columns: ':not(:first-child)'
                    }
                }
            ]
        });

        setInterval(function() {
            fetch("{{ route('enforcer.check-lock') }}", {
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({})
                })
                .then(response => response.text())
                .then(status => {
                    if (status.trim() === "locked") {
                        alert("Your account has been locked by the admin. You will be logged out.");
                        window.location.href = "{{ route('enforcer.logout') }}"; // Adjust if your logout route is different
                    }
                })
                .catch(error => {
                    console.error("Lock check failed:", error);
                });
        }, 5000); // every 5 seconds
    </script>
    @endsection