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
                                    <td>{{ \Carbon\Carbon::parse($fine->issued_date)->format('M. d, Y') }}</td>
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
        // ✅ Initialize DataTable
        var table = $('#dataTable').DataTable({
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'csv',
                    className: 'btn btn-primary mb-3',
                    text: '<i class="fas fa-file-csv"></i> CSV', // CSV icon
                    exportOptions: {
                        columns: ':not(:first-child)'
                    }
                },
                {
                    extend: 'excel',
                    className: 'btn btn-success mb-3',
                    text: '<i class="fas fa-file-excel"></i> Excel', // Excel icon
                    exportOptions: {
                        columns: ':not(:first-child)'
                    }
                },
                {
                    extend: 'pdf',
                    className: 'btn btn-danger mb-3',
                    text: '<i class="fas fa-file-pdf"></i> PDF', // PDF icon
                    exportOptions: {
                        columns: ':not(:first-child)'
                    }
                },
                {
                    extend: 'print',
                    className: 'btn btn-dark mb-3',
                    text: '<i class="fas fa-print"></i> Print', // Print icon
                    exportOptions: {
                        columns: ':not(:first-child)'
                    }
                }
            ],

            language: {
                sSearch: "",
                sSearchPlaceholder: "Search...",
                sEmptyTable: "No data available in table",
                sInfo: "Showing _START_ to _END_ of _TOTAL_ entries",
                sInfoEmpty: "Showing 0 to 0 of 0 entries",
                sInfoFiltered: "(filtered from _MAX_ total entries)",
                sLengthMenu: "Show _MENU_ entries",
                sLoadingRecords: "Loading...",
                sProcessing: "Processing...",
                sZeroRecords: "No matching records found"
            },

            initComplete: function() {
                let api = this.api();

                // ✅ Style the search box and add search icon
                const $filter = $('.dataTables_filter');
                $filter.addClass('position-relative');

                const $input = $filter.find('input');
                $input
                    .attr('placeholder', 'Search...')
                    .addClass('form-control')
                    .css({
                        'padding-left': '30px',
                        'width': '150px'
                    });

                // Add search icon inside label
                $filter.find('label').prepend('<i class="fas fa-search search-icon position-absolute"></i>');

                // ✅ Append dropdown beside search bar
                $("#dataTable_filter").append(`
                <label class="ml-3">
                    <select id="violationFilter" class="form-control form-control-sm">
                        <option value="">Filter Violations</option>
                    </select>
                </label>
    `);

                // ✅ Collect all unique violations from the table
                var violations = [];
                api.column(2).data().each(function(value) {
                    if (value && !violations.includes(value)) {
                        violations.push(value);
                    }
                });

                // ✅ Populate dropdown
                violations.sort().forEach(v => {
                    $('#violationFilter').append(`<option value="${v}">${v}</option>`);
                });

                // ✅ Filter table when dropdown changes
                $('#violationFilter').on('change', function() {
                    api.column(2).search(this.value).draw();
                });
            }
        });

        // 🔐 Account lock check every 5 seconds
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
                        window.location.href = "{{ route('enforcer.logout') }}";
                    }
                })
                .catch(error => console.error("Lock check failed:", error));
        }, 5000);
    </script>
    @endsection