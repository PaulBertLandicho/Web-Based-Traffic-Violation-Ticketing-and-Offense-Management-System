@extends('layouts.layout')
@section('title', 'View Traffic Officer | Traffic Administrative')

@section('content')
@include('layouts.components.admin.topNav')
@include('layouts.components.admin.leftsideNavbar')
@include('layouts.components.admin.modals.enforcer_details_modal')

<div class="dashwrapper">
    <div class="container-fluid">
        <h1 class="mt-4">View All Traffic Enforcers</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="">Dashboard</a></li>
            <li class="breadcrumb-item active">View All Traffic Enforcers</li>
        </ol>

        <!-- Lock All Button -->
        <button id="toggleAllBtn" class="btn btn-primary mb-3" data-status="unlock">
            <i class="fas fa-lock"></i> Lock All Officers
        </button>

        <div class="card mt-5 mb-4">
            <div class="card-header">
                <i class="fas fa-table mr-1"></i> You can sort data here
            </div>
            <div class="card-body">
                <div class="table-responsive" id="employee_table">
                    <table class="table table-striped table-bordered" id="dataTable">
                        <thead>
                            <tr>
                                <th>Action</th>
                                <th>Enforcer ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Assigned Area</th>
                                <th>Status</th>
                                <th>Total Fines Issued</th>
                                <th>Total Amount Collected</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($enforcers as $enforcer)
                            <tr>
                                <td>
                                    <button class="btn btn-info btn-sm view_data" data-id="{{ $enforcer->enforcer_id }}"><i class="fas fa-eye"></i></button>
                                    <button class="btn btn-success btn-sm edit_data" data-id="{{ $enforcer->enforcer_id }}"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-danger btn-sm delete_data" data-id="{{ $enforcer->enforcer_id }}"><i class="fas fa-trash-alt"></i></button>

                                    <button class="btn toggle-btn {{ $enforcer->is_locked ? 'btn-danger' : 'btn-warning' }}"
                                        data-id="{{ $enforcer->enforcer_id }}"
                                        data-status="{{ $enforcer->is_locked ? 'locked' : 'unlocked' }}">
                                        <i class="fas {{ $enforcer->is_locked ? 'fa-lock' : 'fa-lock-open' }}"></i>
                                        {{ $enforcer->is_locked ? ' Unlock' : ' Lock' }}
                                    </button>
                                </td>
                                <td>{{ $enforcer->enforcer_id }}</td>
                                <td>{{ $enforcer->enforcer_name }}</td>
                                <td>{{ $enforcer->enforcer_email }}</td>
                                <td>{{ $enforcer->assigned_area }}</td>
                                <td>
                                    <span class="badge {{ $enforcer->is_locked ? 'bg-danger' : 'bg-success' }}">
                                        {{ $enforcer->is_locked ? 'Locked' : 'Unlocked' }}
                                    </span>
                                </td>
                                @php
                                $stats = $fineStats[$enforcer->enforcer_id] ?? null;
                                @endphp
                                <td>{{ $stats ? $stats->reported_fine_count : 0 }}</td>
                                <td>₱{{ $stats ? number_format($stats->reported_fine_amount, 2) : '0.00' }}</td>
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
            ]
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).on('click', '.view_data', function() {
            const id = $(this).data('id'); // Get from button attribute

            $.post('/admin/enforcer/details', {
                id: id
            }, function(res) {
                if (res.enforcer) {
                    $('#enforcer_detail').html(`
                <p><strong>Name:</strong> ${res.enforcer.enforcer_name}</p>
                <p><strong>Email:</strong> ${res.enforcer.enforcer_email ?? 'N/A'}</p>
                <p><strong>Assigned Area:</strong> ${res.enforcer.assigned_area}</p>
            `);
                    $('#dataModal').modal('show');
                } else {
                    Swal.fire('Error', 'Enforcer details not found.', 'error');
                }
            }).fail(() => {
                Swal.fire('Error', 'Could not load enforcer details.', 'error');
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

        // 🟢 Delete enforcer
        $(document).on('click', '.delete_data', function() {
            const id = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: 'This will permanently delete the enforcer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('/admin/enforcer/delete', {
                        did: id
                    }, function(res) {
                        if (res.success) {
                            Swal.fire('Deleted!', res.success, 'success');
                            // Remove row instantly without reloading
                            $(`.delete_data[data-id="${id}"]`).closest('tr').remove();
                        } else {
                            Swal.fire('Error', res.error || 'Deletion failed.', 'error');
                        }
                    }).fail(() => {
                        Swal.fire('Error', 'Could not delete enforcer.', 'error');
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
                                .html('<i class="fas fa-lock-open"></i> Lock')
                                .data('status', 'unlocked');
                            button.closest('tr').find('td:nth-child(6) span')
                                .removeClass('bg-danger').addClass('bg-success').text('Unlocked');
                        } else {
                            button.removeClass('btn-warning').addClass('btn-danger')
                                .html('<i class="fas fa-lock"></i> Unlock')
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
                                .removeClass('btn-warning').addClass('btn-primary');
                        } else {
                            button.html('<i class="fas fa-lock-open"></i> Unlock All Officers')
                                .data('status', 'lock')
                                .removeClass('btn-primary').addClass('btn-warning');
                        }
                        rows.each(function() {
                            const statusCell = $(this).find('td:nth-child(6) span');
                            const toggleBtn = $(this).find('.toggle-btn');
                            if (isLocked) {
                                statusCell.removeClass('bg-success').addClass('bg-danger').text('Locked');
                                toggleBtn.removeClass('btn-warning').addClass('btn-danger')
                                    .html('<i class="fas fa-lock"></i> Unlock')
                                    .data('status', 'locked');
                            } else {
                                statusCell.removeClass('bg-danger').addClass('bg-success').text('Unlocked');
                                toggleBtn.removeClass('btn-danger').addClass('btn-warning')
                                    .html('<i class="fas fa-lock-open"></i> Lock')
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
</script>

@endsection