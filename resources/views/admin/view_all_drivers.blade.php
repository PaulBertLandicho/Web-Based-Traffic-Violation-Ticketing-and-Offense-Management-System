@extends('layouts.layout')
@section('title', 'View All Drivers | Traffic Administrative')

@section('content')
@include('layouts.components.admin.topNav')
@include('layouts.components.admin.leftsideNavbar')
@include('layouts.components.admin.modals.driver_details_modal')
@include('layouts.components.footer')

<div class="dashwrapper">
    <div class="container-fluid">
        <h1 class="mt-4">Manage Driver Violation Records</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('admin.admin-dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Manage Driver Violation Records</li>
        </ol>
        <div>
            <a href="javascript:void(0);" data-toggle="modal" data-target="#archivedDriversModal" class="btn btn-secondary">
                <i class="fas fa-archive"></i> View Archived Drivers
            </a>
        </div>
        <!-- Archived Enforcers Modal -->
        <div class="modal fade" id="archivedDriversModal" tabindex="-1" role="dialog" aria-labelledby="archivedDriversModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="archivedDriversModalLabel">Archived Drivers</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                    @elseif(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    <div class="modal-body">
                        <table class="table table-bordered" id="archivedTable">
                            <thead>
                                <tr>
                                    <th>License ID</th>
                                    <th>Driver Name</th>
                                    <th>License Type</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Will be populated via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

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

        <div class="card mt-4 mb-5">
            <div class="card-header">
                <i class="fas fa-table mr-1"></i> Manage Driver's
            </div>

            <div class="card-body">
                <div class="table-responsive" id="employee_table">
                    <table class="table table-bordered" id="dataTable">
                        <thead>
                            <tr>
                                <th>Action</th>
                                <th>License ID</th>
                                <th>License Type</th>
                                <th>Driver Full Name</th>
                                <th>License Issue Date</th>
                                <th>License Expire Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($drivers as $driver)
                            <tr>
                                <td>
                                    <button class="btn btn-info btn-sm view_data" data-id="{{ $driver->license_id }}"><i class="fas fa-eye"></i></button>
                                    <button class="btn btn-success btn-sm edit_data" data-id="{{ $driver->license_id }}"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-secondary btn-sm archive_data" data-id="{{ $driver->license_id }}"><i class="fas fa-archive"></i></button>
                                </td>
                                <td>{{ $driver->license_id }}</td>
                                <td>{{ $driver->license_type }}</td>
                                <td>{{ $driver->driver_name }}</td>
                                <td>{{ \Carbon\Carbon::parse($driver->license_issue_date)->format('M. d, Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($driver->license_expire_date)->format('M. d, Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- ✅ jQuery, Bootstrap, and SweetAlert2 -->
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
    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();

        $('#dataTable').DataTable({
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
                const $filter = $('.dataTables_filter');
                $filter.addClass('position-relative');

                const $input = $filter.find('input');
                $input
                    .attr('placeholder', 'Search...')
                    .addClass('form-control')
                    .css({
                        'padding-left': '30px',
                        'width': '200px'
                    });

                $filter.find('label').prepend('<i class="fas fa-search search-icon"></i>');
            }
        });
    });

    $(document).ready(function() {
        $('#dataTable').DataTable();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });


        $(document).on('click', '.view_data', function() {
            const id = $(this).data('id');
            $.post('/admin/driver/details', {
                did: id
            }, function(res) {
                const driver = res.driver;
                const violations = res.violations;

                let violationsHTML = '';

                if (violations.length) {

                    violationsHTML +=
                        '<div class="table-responsive" style="overflow-x:auto;">' +
                        '<table id="driverViolationsTable" class="table table-hover align-middle rounded">' +
                        '<thead class="table-light">' +
                        '<tr>' +
                        '<th>Traffic Enforcer</th>' +
                        '<th>Issued Date</th>' +
                        '<th>Violation Type</th>' +
                        '<th>Amount</th>' +
                        '<th>Vehicle No</th>' +
                        '<th>Vehicle Type</th>' +
                        '<th>Place</th>' +
                        '<th>Penalty Applied</th>' +
                        '<th>Offense #</th>' +
                        '<th>Status</th>' +

                        '</tr>' +
                        '</thead>' +
                        '<tbody>';


                    violations.forEach(v => {


                        // Format issued date
                        const issued = new Date(v.issued_date + ' ' + (v.issued_time || '00:00:00'));
                        const formattedIssued = issued.toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric'
                        }) + (v.issued_time ? ' ' + v.issued_time : '');

                        // Format due/expire date
                        const due = new Date(v.expire_date);
                        const now = new Date();
                        const formattedDueDate = due.toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric'
                        });

                        // Determine Status
                        let statusBadge = '';
                        if (v.status === 'paid') {
                            statusBadge = '<span class="badge bg-success">Paid </span>';
                        } else if (due < now) {
                            statusBadge = '<span class="badge bg-danger">Overdue </span>';
                        } else {
                            statusBadge = '<span class="badge bg-warning text-dark">Pending </span>';
                        }

                        // Status + Due Date combined
                        let statusWithDue = statusBadge + '<br><small class="text-muted">- Due: ' + formattedDueDate + '</small>';

                        // Build Row with extra details
                        violationsHTML +=
                            '<tr>' +
                            '<td>[' + (v.enforcer_id || 'N/A') + '] ' + (v.enforcer_name || 'N/A') + '</td>' +
                            '<td>' + formattedIssued + '</td>' +
                            '<td>' + v.violation_type + '</td>' +
                            '<td>₱' + parseFloat(v.total_amount).toFixed(2) + '</td>' +
                            '<td>' + (v.vehicle_no || 'N/A') + '</td>' +
                            '<td>' + (v.vehicle_type || 'N/A') + '</td>' +
                            '<td>' + (v.place || 'N/A') + '</td>' +
                            '<td>' + (v.penalty_applied ? 'Yes' : 'No') + '</td>' +
                            '<td>' + (v.offense_number || '1') + '</td>' +
                            '<td>' + statusWithDue + '</td>' +
                            '</tr>';
                    });


                    violationsHTML +=
                        '</tbody>' +
                        '</table>' +
                        '</div>';

                } else {

                    violationsHTML =
                        '<div class="text-center text-muted mt-2">' +
                        '<i class="fas fa-check-circle"></i> No Violations Recorded' +
                        '</div>';
                }


                const baseAsset = "{{ asset('') }}";
                const signaturePath = driver.driver_signature ?
                    baseAsset + driver.driver_signature :
                    "{{ asset('assets/img/no-signature.png') }}";

                // Format Date of Birth
                let formattedDOB = driver.date_of_birth ?
                    new Date(driver.date_of_birth).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short', // "Nov"
                        day: 'numeric'
                    }) :
                    'N/A';

                $('#driver_detail').html(`
<div class="card border-0 shadow-sm mb-4 rounded-4">
    <div class="card-body">
        <h5 class="fw-semibold text-info mb-3"><i class="fas fa-user me-2"></i> Personal Information</h5>
        <div class="row g-3">
            <div class="col-md-6"><p class="mb-1 text-muted fw-bold">Name</p><p>${driver.driver_name}</p></div>
            <div class="col-md-6"><p class="mb-1 text-muted fw-bold">License ID</p><p>${driver.license_id}</p></div>
            <div class="col-md-6"><p class="mb-1 text-muted fw-bold">Address</p><p>${driver.home_address || 'N/A'}</p></div>
            <div class="col-md-6"><p class="mb-1 text-muted fw-bold">Date of Birth</p><p>${formattedDOB}</p></div>
            <div class="col-md-6"><p class="mb-1 text-muted fw-bold">License Type</p><p>${driver.license_type || 'N/A'}</p></div>
            <div class="col-md-6"><p class="mb-1 text-muted fw-bold">Contact No</p><p>${driver.contact_no || 'N/A'}</p></div>
            <div class="col-md-12 text-center mt-4">
                <p class="mb-1 text-muted fw-bold">Driver Signature</p>
                <img src="${signaturePath}" alt="Driver Signature" 
                     class="img-fluid border rounded p-2 bg-light shadow-sm"
                     style="max-height: 120px; object-fit: contain;">
            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <h5 class="fw-semibold text-danger mb-3"><i class="fas fa-gavel me-2"></i> Driver History Violations Details</h5>
                        ${violationsHTML}
                    </div>
                </div>
            `);
                // Destroy existing DataTable if exists
                if ($.fn.DataTable.isDataTable('#driverViolationsTable')) {
                    $('#driverViolationsTable').DataTable().destroy();
                }

                // Initialize DataTable with PDF button
                $('#driverViolationsTable').DataTable({
                    dom: 'Bfrtip',
                    buttons: [{
                        extend: 'pdfHtml5',
                        className: 'btn btn-danger mb-2',
                        title: 'Driver History Violations',
                        text: '<i class="fas fa-file-pdf"></i> Export PDF',
                        exportOptions: {
                            columns: ':visible' // export all visible columns
                        },
                        orientation: 'landscape',
                        pageSize: 'A4'
                    }],
                    pageLength: 5,
                });

                $('#dataModal').modal('show');
            });
        });


        // ✅ Edit Driver (still works after search/filter)
        $(document).on('click', '.edit_data', function() {
            const id = $(this).data('id');
            $.post('/admin/driver/details', {
                did: id
            }, function(res) {
                $('#edit_license_id').val(res.driver.license_id);
                $('#edit_driver_email').val(res.driver.driver_email);
                $('#edit_driver_name').val(res.driver.driver_name);
                $('#edit_home_address').val(res.driver.home_address);
                $('#edit_license_issue_date').val(res.driver.license_issue_date);
                $('#edit_license_expire_date').val(res.driver.license_expire_date);
                $('#edit_data_Modal').modal('show');
            });
        });

        $('#edit_form').submit(function(e) {
            e.preventDefault();

            $.post('/admin/driver/update', $(this).serialize(), function(res) {
                $('#edit_data_Modal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Updated!',
                    text: res.success,
                    toast: true,
                    position: 'top-end',
                    timer: 3000,
                    showConfirmButton: false
                });

                // ✅ Update DataTable row
                let table = $('#dataTable').DataTable();
                let id = $('#edit_license_id').val();

                let row = table.row($(`button[data-id="${id}"]`).parents('tr'));
                let rowData = row.data();

                // Update the columns in your table (adjust indexes)
                rowData[1] = $('#edit_driver_name').val(); // example: Name column
                rowData[2] = $('#edit_home_address').val(); // example: Address column
                rowData[3] = $('#edit_license_issue_date').val(); // example: Issue Date
                rowData[4] = $('#edit_license_expire_date').val(); // example: Expire Date

                row.data(rowData).draw(false);

            }).fail(() => {
                Swal.fire('Error', 'Update failed. Check the form.', 'error');
            });
        });

        // 🟢 Load Archived Drivers Modal dynamically
        $('#archivedDriversModal').on('show.bs.modal', function() {
            const tbody = $('#archivedTable tbody');
            tbody.html('<tr><td colspan="4" class="text-center">Loading...</td></tr>');

            $.get('{{ route("drivers.archived") }}', function(res) {
                tbody.empty();
                if (!res.drivers.length) {
                    tbody.html('<tr><td colspan="4" class="text-center">No archived drivers found.</td></tr>');
                    return;
                }
                res.drivers.forEach(driver => {
                    tbody.append(`
                    <tr>
                        <td>${driver.license_id}</td>
                        <td>${driver.driver_name}</td>
                        <td>${driver.license_type ?? 'N/A'}</td>
                        <td>
                            <form action="/admin/drivers/restore/${driver.license_id}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">Restore</button>
                            </form>
                        </td>
                    </tr>
                `);
                });
            });
        });


        // ✅ Archive Driver (works after search/filter)
        $(document).on('click', '.archive_data', function() {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Archive Driver?',
                text: 'Driver will be archived if fines are paid.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#6c757d',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, archive'
            }).then(result => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/admin/driver/archive',
                        type: 'POST',
                        data: {
                            did: id
                        },
                        success: function(res) {
                            Swal.fire('Archived!', res.success, 'success');
                            table.row($(`button[data-id="${id}"]`).parents('tr')).remove().draw();
                        },
                        error: function(xhr) {
                            if (xhr.status === 400 && xhr.responseJSON?.error) {
                                Swal.fire('Cannot Archive', xhr.responseJSON.error, 'warning');
                            } else {
                                Swal.fire('Error', 'Server error occurred.', 'error');
                            }
                        }
                    });
                }
            });
        });
    });
</script>

@endsection