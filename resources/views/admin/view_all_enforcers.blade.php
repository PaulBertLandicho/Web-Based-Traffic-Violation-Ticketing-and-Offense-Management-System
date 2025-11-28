@extends('layouts.layout')
@section('title', 'View Traffic Officer | Traffic Administrative')

@section('content')
@include('layouts.components.admin.topNav')
@include('layouts.components.admin.leftsideNavbar')
@include('layouts.components.admin.modals.enforcer_details_modal')
@include('layouts.components.footer')

<div class="dashwrapper">
    <div class="container-fluid">
        <h1 class="mt-4">Manage Traffic Enforcers</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="">Dashboard</a></li>
            <li class="breadcrumb-item active">Manage All Traffic Enforcers</li>
        </ol>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <button id="toggleAllBtn" class="btn btn-primary" data-status="unlock">
                    <i class="fas fa-lock"></i> Lock All Officers
                </button>
            </div>
            <div>
                <a href="javascript:void(0);" data-toggle="modal" data-target="#archivedEnforcerModal" class="btn btn-secondary">
                    <i class="fas fa-archive"></i> View Archived
                </a>
            </div>
        </div>

        <div class="modal fade" id="issueViolationModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="issueViolationForm">
                        <div class="modal-header bg-danger">
                            <h4 class="modal-title text-white"><i class="fas fa-pen"></i> Issue Violation to Enforcer</h4>
                            <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="enforcer_id" id="violation_enforcer_id">
                            <div class="form-group">
                                <label>Violation Type</label>
                                <input type="text" name="violation_type" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Details</label>
                                <textarea name="details" class="form-control"></textarea>
                            </div>
                            <div class="form-group">
                                <label>Penalty Amount (₱)</label>
                                <input type="number" name="penalty_amount" class="form-control" step="0.01" min="0">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-danger">Issue Violation</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Archived Enforcers Modal -->
        <div class="modal fade" id="archivedEnforcerModal" tabindex="-1" role="dialog" aria-labelledby="archivedEnforcerModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="archivedEnforcerModalLabel">Archived Enforcers</h5>
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
                                    <th>Enforcer ID</th>
                                    <th>Name</th>
                                    <th>Assigned Area</th>
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

        <div class="card mt-2 mb-4">
            <div class="card-header">
                <i class="fas fa-table mr-1"></i> Manage Traffic Enforcers
            </div>
            <div class="card-body">
                <div class="table-responsive" id="employee_table">
                    <table class="table table-striped table-bordered" id="dataTable">
                        <thead>
                            <tr>
                                <th>Action</th>
                                <th>Enforcer ID</th>
                                <th>Name</th>
                                <th>Assigned Area</th>
                                <th>Gender</th>
                                <th>Total Fines Issued</th>
                                <th>Total Amount Collected</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            // ✅ Rank enforcers by amount collected
                            $sortedCollectors = collect($fineStats)->sortByDesc('reported_fine_amount')->values();

                            $topCollectorId = $sortedCollectors->get(0)->enforcer_id ?? null;
                            $secondCollectorId = $sortedCollectors->get(1)->enforcer_id ?? null;
                            $lowestCollectorId = $sortedCollectors->last()->enforcer_id ?? null;
                            @endphp

                            @foreach ($enforcers as $enforcer)
                            @php
                            $stats = $fineStats[$enforcer->enforcer_id] ?? null;

                            // Check if new (no fines yet)
                            $isNew = !$stats || $stats->reported_fine_count == 0;

                            // Determine collector ranks
                            $isTopCollector = $enforcer->enforcer_id == $topCollectorId && $stats && $stats->reported_fine_amount > 0;
                            $isSecondCollector = $enforcer->enforcer_id == $secondCollectorId && $stats && $stats->reported_fine_amount > 0;
                            $isLowestCollector = $enforcer->enforcer_id == $lowestCollectorId && $stats && $stats->reported_fine_amount > 0;
                            @endphp

                            <tr>
                                <td>
                                    <button class="btn btn-info btn-sm view_data" data-id="{{ $enforcer->enforcer_id }}"><i class="fas fa-eye"></i></button>
                                    <button class="btn btn-success btn-sm edit_data" data-id="{{ $enforcer->enforcer_id }}"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-secondary btn-sm archive_data" data-id="{{ $enforcer->enforcer_id }}"><i class="fas fa-archive"></i></button>
                                    <button class="btn toggle-btn {{ $enforcer->is_locked ? 'btn-danger' : 'btn-warning' }}"
                                        data-id="{{ $enforcer->enforcer_id }}"
                                        data-status="{{ $enforcer->is_locked ? 'locked' : 'unlocked' }}">
                                        <i class="fas {{ $enforcer->is_locked ? 'fa-lock' : 'fa-lock-open' }}"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm issue_violation" data-id="{{ $enforcer->enforcer_id }}">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </button>
                                </td>

                                <!-- Enforcer ID column -->
                                <td>
                                    {{ $enforcer->enforcer_id }}
                                    @if($isNew)
                                    <span class="badge bg-success">New Enforcer</span>
                                    @endif
                                </td>

                                <td>{{ $enforcer->enforcer_name }}</td>
                                <td>{{ $enforcer->assigned_area }}</td>
                                <td>{{ $enforcer->gender }}</td>
                                <td>{{ $stats ? $stats->reported_fine_count : 0 }}</td>

                                <!-- Total Amount Collected column -->
                                <td>
                                    ₱{{ $stats ? number_format($stats->reported_fine_amount, 2) : '0.00' }}
                                    @if($isTopCollector)
                                    <span class="badge bg-primary">High Collected</span>
                                    @elseif($isSecondCollector)
                                    <span class="badge bg-warning text-dark">Second High</span>
                                    @elseif($isLowestCollector)
                                    <span class="badge bg-secondary">Lowest Collected</span>
                                    @endif
                                </td>

                                <td>
                                    <span class="badge {{ $enforcer->is_locked ? 'bg-danger' : 'bg-success' }}">
                                        {{ $enforcer->is_locked ? 'Locked' : 'Unlocked' }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="{{ asset('assets/vendors/jquery/jquery-3.5.1.js') }}"></script>
<script src="{{ asset('assets/vendors/bootstrap/popper.min.js') }}"></script>
<script src="{{ asset('assets/vendors/bootstrap/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/vendors/DataTables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/vendors/DataTables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/vendors/DataTables/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('assets/vendors/DataTables/jszip.min.js') }}"></script>
<script src="{{ asset('assets/vendors/DataTables/pdfmake.min.js') }}"></script>
<script src="{{ asset('assets/vendors/DataTables/vfs_fonts.js') }}"></script>
<script src="{{ asset('assets/vendors/DataTables/buttons.html5.min.js') }}"></script>
<script src="{{ asset('assets/vendors/DataTables/buttons.print.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'csv',
                    className: 'btn btn-primary mb-3',
                    exportOptions: {
                        columns: ':not(:first-child)'
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
                        'width': '200px'
                    });

                // Add search icon inside label
                $filter.find('label').prepend('<i class="fas fa-search search-icon position-absolute"></i>');


                // Add dropdown beside search bar
                $("#dataTable_filter").append(`
                <label class="ml-2">
                    <select id="barangayFilter" class="form-control form-control-sm">
                        <option value="">Filter by Barangay</option>
                    </select>
                </label>
            `);

                // Collect unique barangays from column 3 (Assigned Area)
                let barangays = [];
                api.column(3).data().each(function(value) {
                    if (value && !barangays.includes(value.trim())) {
                        barangays.push(value.trim());
                    }
                });

                barangays.sort();

                // Populate dropdown
                barangays.forEach(function(b) {
                    $('#barangayFilter').append(`<option value="${b}">${b}</option>`);
                });

                // Filter table when barangay selected
                $('#barangayFilter').on('change', function() {
                    let selected = $(this).val();
                    api.column(3).search(selected).draw();
                });
            }
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).on('click', '.view_data', function() {
            const id = $(this).data('id');

            $.post('/admin/enforcer/details', {
                id: id
            }, function(res) {
                if (!res.enforcer) {
                    Swal.fire('Error', 'Enforcer details not found.', 'error');
                    return;
                }

                // =========================
                // ENFORCER INFO
                // ✅ Display profile image (or default if not uploaded)
                let imgPath = res.enforcer.profile_image ?
                    `/${res.enforcer.profile_image}` :
                    '/assets/img/default-enforcer.png';
                // ✅ Display enforcer signature (if available)
                let signaturePath = res.enforcer.enforcer_signature ?
                    `/${res.enforcer.enforcer_signature}` :
                    '/assets/img/no-signature.png';
                $('#detail_signature').attr('src', signaturePath);

                $('#detail_image').attr('src', imgPath);
                $('#detail_name').text(res.enforcer.enforcer_name);
                $('#detail_email').text(res.enforcer.enforcer_email ?? 'N/A');
                $('#detail_area').text(res.enforcer.assigned_area ?? 'N/A');
                $('#detail_contact').text(res.enforcer.contact_no ?? 'N/A');

                // =========================
                // ENFORCER VIOLATIONS FILED (against this enforcer)
                let enforcerTbody = $('#enforcerViolationsTable tbody');
                enforcerTbody.empty();

                if (res.enforcer.is_locked) {
                    Swal.fire('Notice', 'This enforcer is LOCKED due to violations filed.', 'warning');
                    $('#detail_name').append(' <span class="badge badge-danger">Locked</span>');

                    // 🛑 Also update toggle button & badge in table
                    let row = $(`.toggle-btn[data-id="${res.enforcer.enforcer_id}"]`).closest('tr');
                    row.find('.toggle-btn')
                        .removeClass('btn-warning').addClass('btn-danger')
                        .html('<i class="fas fa-lock"></i>')
                        .data('status', 'locked');
                    row.find('td:last span')
                        .removeClass('bg-success').addClass('bg-danger')
                        .text('Locked');
                }

                // =========================
                // DRIVER VIOLATIONS (issued by this enforcer)
                let violations = [];
                let driverData = [];

                if (res.drivers && res.drivers.length > 0) {
                    res.drivers.forEach(d => {
                        driverData.push([
                            d.license_id,
                            d.driver_name,
                            d.violation_type,
                            `₱${parseFloat(d.total_amount).toFixed(2)}`,
                            d.created_at
                        ]);
                        violations.push(d.violation_type);
                    });
                }

                // Destroy previous DataTable if exists
                if ($.fn.DataTable.isDataTable('#violationsTable')) {
                    $('#violationsTable').DataTable().clear().destroy();
                }

                // ENFORCER COMPLAINT HISTORY
                let complaintTbody = $('#complaintHistoryTable tbody');
                complaintTbody.empty();

                if (res.violations && res.violations.length > 0) {
                    // Only include settled violations
                    let settledViolations = res.violations.filter(v => v.status === 'settled');

                    if (settledViolations.length > 0) {
                        settledViolations.forEach(v => {
                            let statusBadge = '<span class="badge badge-success">Settled</span>';

                            complaintTbody.append(`
                <tr>
                    <td>${v.violation_type ?? 'N/A'}</td>
                    <td>${v.details ?? 'N/A'}</td>
                    <td>₱${v.penalty_amount ? Number(v.penalty_amount).toFixed(2) : '0.00'}</td>
                    <td>${new Date(v.created_at).toLocaleString()}</td>
                    <td>${statusBadge}</td>
                    <td>${v.remarks ?? 'N/A'}</td>
                    <td>${v.settled_at ? new Date(v.settled_at).toLocaleString() : new Date().toLocaleString()}</td>
                </tr>
            `);
                        });
                    } else {
                        complaintTbody.append(`<tr><td colspan="7" class="text-center">No settled violations.</td></tr>`);
                    }
                } else {
                    complaintTbody.append(`<tr><td colspan="7" class="text-center">No violations filed.</td></tr>`);
                }



                // Reinitialize DataTable
                let table = $('#violationsTable').DataTable({
                    data: driverData,
                    columns: [{
                            title: "License ID"
                        },
                        {
                            title: "Driver Name"
                        },
                        {
                            title: "Violation"
                        },
                        {
                            title: "Total Amount"
                        },
                        {
                            title: "Date"
                        }
                    ],
                    pageLength: 5,
                    lengthChange: false,
                    initComplete: function() {
                        // ✅ Clear old search bar first to prevent duplicates
                        $("#customSearch").empty();

                        // Move DataTables search box into custom container
                        $("#violationsTable_filter").appendTo("#customSearch");
                        $("#violationsTable_filter label").addClass("mb-0");
                        $("#violationsTable_filter input")
                            .addClass("form-control")
                            .css("width", "250px");
                    }
                });
                // ✅ Add search icon inside the label
                $("#violationsTable_filter label").prepend(`
                <i class="fas fa-search search-icon position-absolute"></i>
        `);

                // Populate violation filter dropdown
                $('#violationFilter').empty().append(`<option value="">Filter by Violation</option>`);
                [...new Set(violations)].sort().forEach(v => {
                    $('#violationFilter').append(`<option value="${v}">${v}</option>`);
                });

                // 🎯 Filter by violation type
                $('#violationFilter').off().on('change', function() {
                    table.column(2).search(this.value).draw();
                });

                // ENFORCER VIOLATIONS FILED (against this enforcer)
                if (res.violations && res.violations.length > 0) {
                    // Track if there are pending violations
                    let hasPending = false;

                    res.violations.forEach(v => {
                        if (v.status === 'pending') { // <-- only display pending violations
                            hasPending = true;

                            let statusBadge = '<span class="badge badge-danger">Pending</span>';
                            let actionBtn = `<button class="btn btn-sm btn-success settleViolationBtn" data-id="${v.id}">
                                <i class="fas fa-check"></i> Settle
                             </button>`;

                            enforcerTbody.append(`
                <tr>
                    <td>${v.violation_type}</td>
                    <td>${v.details ?? 'N/A'}</td>
                    <td>₱${parseFloat(v.penalty_amount).toFixed(2)}</td>
                    <td>${new Date(v.date_issued).toLocaleString()}</td>
                    <td>${statusBadge}</td>
                    <td>${v.remarks ?? 'N/A'}</td>
                    <td>${actionBtn}</td>
                </tr>
            `);
                        }
                    });

                    // If no pending violations, show placeholder
                    if (!hasPending) {
                        enforcerTbody.append(`<tr><td colspan="7" class="text-center">No pending violations.</td></tr>`);
                    }
                } else {
                    enforcerTbody.append(`<tr><td colspan="7" class="text-center">No violations filed.</td></tr>`);
                }

                // Show modal
                $('#dataModal').modal('show');
            }).fail(err => {
                Swal.fire('Error', 'Could not load enforcer details.', 'error');
                console.error(err.responseText);
            });
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).on('click', '.settleViolationBtn', function() {
            const violationId = $(this).data('id');
            const $row = $(this).closest('tr');

            // Disable interaction with modal while processing
            $('#dataModal').attr('inert', true);

            Swal.fire({
                title: 'Add Remarks Before Settlement',
                input: 'textarea',
                inputLabel: 'Remarks (optional)',
                inputPlaceholder: 'Enter any notes before settling...',
                showCancelButton: true,
                confirmButtonText: 'Settle Violation',
                preConfirm: (remarks) => {
                    return $.ajax({
                        url: '/admin/enforcer/violation/settle',
                        method: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            id: violationId,
                            remarks: remarks
                        },
                        dataType: 'json'
                    }).then(res => {
                        if (!res.success) throw new Error(res.message || 'Failed to settle violation');
                        return res; // return the settled violation info
                    }).catch(jqXHR => {
                        let message = 'Request failed';
                        if (jqXHR.responseJSON && jqXHR.responseJSON.message) message = jqXHR.responseJSON.message;
                        Swal.showValidationMessage(`Request failed: ${message}`);
                    });
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    const res = result.value;

                    // 1️⃣ Remove the settled violation from the Violations Complaint Filed table
                    $row.remove();

                    // 2️⃣ Append it to the Complaint / Violation History table
                    let historyTbody = $('#complaintHistoryTable tbody');

                    // Remove placeholder row if present
                    if (historyTbody.find('tr td').length === 1) historyTbody.empty();

                    historyTbody.append(`
                <tr>
                    <td>${res.violation_type}</td>
                    <td>${res.details ?? 'N/A'}</td>
                    <td>₱${parseFloat(res.penalty_amount).toFixed(2)}</td>
                    <td>${new Date(res.date_issued).toLocaleString()}</td>
                    <td><span class="badge badge-success">Settled</span></td>
                    <td>${res.remarks ?? 'N/A'}</td>
                    <td>${new Date().toLocaleString()}</td>
                </tr>
            `);

                    Swal.fire('Success', 'Violation settled successfully.', 'success');

                    // 3️⃣ Optional: Unlock enforcer if no pending violations
                    if (res.unlocked) {
                        Swal.fire('Info', 'All pending violations cleared. Enforcer unlocked.', 'info');
                    }

                    // Remove modal inert state
                    $('#dataModal').removeAttr('inert');
                }
            });
        });




        // 🟢 Edit enforcer
        $('.edit_data').click(function() {
            const id = $(this).data('id');
            $.post('/admin/enforcer/details', {
                id
            }, function(res) {
                if (res && res.enforcer) {
                    const e = res.enforcer;
                    $('#edit_enforcer_id').val(e.enforcer_id);
                    $('#edit_enforcer_email').val(e.enforcer_email);
                    $('#edit_enforcer_name').val(e.enforcer_name);
                    $('#edit_assigned_area').val(e.assigned_area);
                    $('#edit_data_Modal').modal('show');
                } else {
                    Swal.fire('Error', 'Enforcer data not found.', 'error');
                }
            }).fail(() => {
                Swal.fire('Error', 'Could not load enforcer details for edit.', 'error');
            });
        });

        // 🟢 Update enforcer
        $('#edit_form').submit(function(e) {
            e.preventDefault();
            $.post('/admin/enforcer/update', $(this).serialize(), function(res) {
                if (res && res.success && res.enforcer) {
                    $('#edit_data_Modal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated!',
                        text: res.success
                    });

                    // Update table instantly
                    const row = $(`.edit_data[data-id="${res.enforcer.enforcer_id}"]`).closest('tr');
                    row.find('td:eq(2)').text(res.enforcer.enforcer_name);
                    row.find('td:eq(3)').text(res.enforcer.enforcer_email);
                    row.find('td:eq(4)').text(res.enforcer.assigned_area);
                } else {
                    Swal.fire('Error', 'Update failed.', 'error');
                }
            });

        });


        // 🟢 Archive enforcer
        $(document).on('click', '.archive_data', function() {
            const id = $(this).data('id');

            Swal.fire({
                title: 'Archive Enforcer?',
                text: 'This will move the enforcer to archive list.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, archive it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('/admin/enforcer/archive', {
                        aid: id
                    }, function(res) {
                        if (res.success) {
                            Swal.fire('Archived!', res.success, 'success');
                            // Remove row instantly without reload
                            $(`.archive_data[data-id="${id}"]`).closest('tr').remove();
                        } else {
                            Swal.fire('Error', res.error || 'Archiving failed.', 'error');
                        }
                    }).fail(() => {
                        Swal.fire('Error', 'Could not archive enforcer.', 'error');
                    });
                }
            });
        });


        // 🟢 Toggle Single Officer
        $('.toggle-btn').click(function() {
            const button = $(this);
            const id = button.data('id');
            fetch("{{ route('enforcer.toggleLock') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                    },
                    body: JSON.stringify({
                        id
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Success', data.message, 'success');
                        const isLocked = button.data('status') === 'locked';
                        if (isLocked) {
                            button.removeClass('btn-danger').addClass('btn-warning')
                                .html('<i class="fas fa-lock-open"></i> ')
                                .data('status', 'unlocked');
                            button.closest('tr').find('td:nth-child(6) span')
                                .removeClass('bg-danger').addClass('bg-success').text('Unlocked');
                        } else {
                            button.removeClass('btn-warning').addClass('btn-danger')
                                .html('<i class="fas fa-lock"></i> ')
                                .data('status', 'locked');
                            button.closest('tr').find('td:nth-child(6) span')
                                .removeClass('bg-success').addClass('bg-danger').text('Locked');
                        }
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(() => Swal.fire('Error', 'Something went wrong.', 'error'));
        });

        // 🟢 Toggle All Officers
        $('#toggleAllBtn').click(function() {
            const button = $(this);
            fetch('/enforcer/toggle-lock-all', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Content-Type': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Success', data.message, 'success');
                        const rows = $('#dataTable tbody tr');
                        const isLocked = data.status === 1;
                        if (isLocked) {
                            button.html('<i class="fas fa-lock"></i> Lock All Officers')
                                .data('status', 'unlock')
                                .removeClass('btn-warning').addClass('btn-danger');
                        } else {
                            button.html('<i class="fas fa-lock-open"></i> Unlock All Officers')
                                .data('status', 'lock')
                                .removeClass('btn-danger').addClass('btn-warning');
                        }
                        rows.each(function() {
                            const statusCell = $(this).find('td:nth-child(6) span');
                            const toggleBtn = $(this).find('.toggle-btn');
                            if (isLocked) {
                                statusCell.removeClass('bg-success').addClass('bg-danger').text('Locked');
                                toggleBtn.removeClass('btn-warning').addClass('btn-danger')
                                    .html('<i class="fas fa-lock"></i> ')
                                    .data('status', 'locked');
                            } else {
                                statusCell.removeClass('bg-danger').addClass('bg-success').text('Unlocked');
                                toggleBtn.removeClass('btn-danger').addClass('btn-warning')
                                    .html('<i class="fas fa-lock-open"></i> ')
                                    .data('status', 'unlocked');
                            }
                        });
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(() => Swal.fire('Error', 'Something went wrong.', 'error'));
        });
    });
    // 🟢 Load Archived Enforcers when modal is opened
    $('#archivedEnforcerModal').on('show.bs.modal', function() {
        $.get("{{ route('enforcers.archived') }}", function(res) {
            let tbody = $('#archivedTable tbody');
            tbody.html('<tr><td colspan="4" class="text-center">Loading...</td></tr>');

            $.get('{{ route("enforcers.archived") }}', function(res) {
                tbody.empty();

                if (res.enforcers && res.enforcers.length > 0) {
                    res.enforcers.forEach(function(e) {
                        tbody.append(`
            <tr>
                <td>${e.enforcer_id}</td>
                <td>${e.enforcer_name}</td>
                <td>${e.assigned_area ?? 'N/A'}</td>
                <td>
                    <button class="btn btn-success btn-sm restore_enforcer" data-id="${e.enforcer_id}">
                        Restore
                    </button>
                </td>
            </tr>
        `);
                    });
                } else {
                    tbody.append(`<tr><td colspan="4" class="text-center">No archived enforcers</td></tr>`);
                }

            });
        });
    });
    $(document).on('click', '.restore_enforcer', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        const rowToRestore = $(this).closest('tr');

        Swal.fire({
            title: 'Restore Enforcer?',
            text: 'This will move the violation back to the active list.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, restore it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("{{ route('enforcers.restore') }}", {
                    rid: id
                }, function(res) {
                    if (res.success) {
                        Swal.fire('Restored!', res.success, 'success');

                        // Remove from archived modal
                        rowToRestore.remove();

                        // Add back to active DataTable
                        const newRow = `
                        <tr>
                            <td>
                                <button class="btn btn-success btn-sm edit_data" data-id="${res.enforcer.enforcer_id}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-secondary btn-sm archive_data" data-id="${res.enforcer.enforcer_id}">
                                    <i class="fas fa-archive"></i>
                                </button>
                            </td>
                            <td>${res.enforcer.enforcer_id}</td>
                            <td>${res.enforcer.enforcer_name}</td>
                            <td>${res.enforcer.assigned_area}</td>
                            <td>${res.enforcer.gender}</td>
                        </tr>
                    `;

                        $('#dataTable tbody').append(newRow);
                    } else {
                        Swal.fire('Error', res.error || 'Restore failed.', 'error');
                    }
                }).fail(() => {
                    Swal.fire('Error', 'Could not restore Enforcer.', 'error');
                });
            }
        });
    });

    $(document).on('click', '.issue_violation', function() {
        let id = $(this).data('id');
        $('#violation_enforcer_id').val(id);
        $('#issueViolationModal').modal('show');
    });

    $('#issueViolationForm').submit(function(e) {
        e.preventDefault();
        $.post("{{ route('enforcer.issueViolation') }}", $(this).serialize(), function(res) {
            $('#issueViolationModal').modal('hide');

            if (res.warning) {
                Swal.fire('Warning', res.warning, 'warning');
            } else if (res.success) {
                Swal.fire('Success', res.success, 'success');

                let row = $(`.issue_violation[data-id="${res.enforcer_id}"]`).closest('tr');
                let toggleBtn = row.find('.toggle-btn');
                toggleBtn.removeClass('btn-warning').addClass('btn-danger')
                    .html('<i class="fas fa-lock"></i>')
                    .data('status', 'locked');

                row.find('td:last span')
                    .removeClass('bg-success').addClass('bg-danger')
                    .text('Locked');
            }
        }).fail(function(err) {
            Swal.fire('Error', 'Could not issue violation.', 'error');
            console.log(err.responseText);
        });
    });
</script>

@endsection