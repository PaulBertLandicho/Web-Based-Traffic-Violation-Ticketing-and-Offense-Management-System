@extends('layouts.layout')
@section('title', 'Add Traffic Violations | Traffic Administrative')

@section('content')
@include('layouts.components.admin.topNav')
@include('layouts.components.admin.leftsideNavbar')
@include('layouts.components.admin.modals.violation_details_modal')

<!-- Dashboard main content start here =================================================-->
<div class="dashwrapper">
    <div class="container-fluid">
        <h1 class="mt-4">Manage Violation Details</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="">Dashboard</a></li>
            <li class="breadcrumb-item active">Provisions Details</li>
        </ol>

        <!-- Archived Traffic Violations Modal -->
        <div class="modal fade" id="archivedViolationModal" tabindex="-1" role="dialog" aria-labelledby="archivedViolationModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="archivedViolationModalLabel">Archived Traffic Violations</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered" id="archivedTable">
                            <thead>
                                <tr>
                                    <th>Violation ID</th>
                                    <th>Violation Type</th>
                                    <th>Violation Amount</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Will be filled dynamically by Ajax -->
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

        <!--Add fine tickets form includes goes here-->
        <div class="card mt-5 mb-4">
            <div class="card-header">
                <i class="fas fa-receipt"></i> Add a Provision Details
            </div>
            <div class="card-body" style="margin:0 2rem 1rem 2rem;">
                <form id="addViolationForm">
                    @csrf
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="fine_id">Provision ID</label>
                            <input type="text" class="form-control" id="fine_id" name="violationid" placeholder="Provision ID">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="provision">Provision</label>
                            <input type="text" class="form-control" id="provision" name="violationtype" placeholder="Provision">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="fine_amount">Fine Amount</label>
                            <input type="number" class="form-control" id="fine_amount" name="violationamount" placeholder="Fine Amount">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus-circle"></i> Add Fine Ticket
                    </button>
                </form>
            </div>
        </div>

        <div>
            <a href="javascript:void(0);" data-toggle="modal" data-target="#archivedViolationModal" class="btn btn-secondary">
                <i class="fas fa-archive"></i> View Archived
            </a>
        </div>

        <div class="card mt-5 mb-4">
            <div class="card-header">
                <i class="fas fa-table mr-1"></i> All Fine Tickets Details
            </div>
            <div class="card-body">
                <div class="table-responsive" id="employee_table">
                    <table class="table table-striped table-bordered" id="dataTable">
                        <thead>
                            <tr>
                                <th>Action</th>
                                <th>Violation ID</th>
                                <th>Violation Type</th>
                                <th>Violation Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @isset($violations)
                            @foreach ($violations as $violation)
                            <tr>
                                <td>
                                    <button class="btn btn-success btn-sm edit_data" data-id="{{ $violation->violation_id }}"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-secondary btn-sm archive_data" data-id="{{ $violation->violation_id }}"><i class="fas fa-archive"></i></button>
                                </td>
                                <td>{{ $violation->violation_id }}</td>
                                <td>{{ $violation->violation_type }}</td>
                                <td>₱{{ $violation->violation_amount }}</td>
                            </tr>
                            @endforeach
                            @endisset
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

        // ✅ Initialize DataTable
        const table = $('#dataTable').DataTable({
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
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        let currentRow; // store the row being edited

        // EDIT HANDLER
        $(document).on('click', '.edit_data', function() {
            const id = $(this).data('id');
            currentRow = $(this).closest('tr');

            $.post('/admin/violation/details', {
                did: id
            }, function(res) {
                const violation = res.traffic_violations;
                if (violation) {
                    $('#edit_violation_id').val(violation.violation_id);
                    $('#violation_type').val(violation.violation_type);
                    $('#violation_amount').val(violation.violation_amount);
                    $('#edit_data_Modal').modal('show');
                } else {
                    Swal.fire('Error', 'Violation data not found.', 'error');
                }
            }).fail(() => {
                Swal.fire('Error', 'Failed to fetch violation details.', 'error');
            });
        });

        // UPDATE HANDLER (no reload)
        $('#edit_form').submit(function(e) {
            e.preventDefault();

            $.post('/admin/violation/update', $(this).serialize(), function(res) {
                $('#edit_data_Modal').modal('hide');

                // Update row data in DataTable
                const updatedType = $('#violation_type').val();
                const updatedAmount = $('#violation_amount').val();

                table.row(currentRow).data([
                    currentRow.find('td').eq(0).html(),
                    $('#edit_violation_id').val(),
                    updatedType,
                    '₱' + updatedAmount
                ]).draw(false);

                Swal.fire({
                    icon: 'success',
                    title: 'Updated!',
                    text: res.success,
                    toast: true,
                    position: 'top-end',
                    timer: 3000,
                    showConfirmButton: false
                });
            }).fail(() => {
                Swal.fire('Error', 'Update failed. Check the form.', 'error');
            });
        });

        // DELETE HANDLER (no reload)
        $(document).on('click', '.archive_data', function() {
            const id = $(this).data('id');
            const rowToDelete = $(this).closest('tr');

            Swal.fire({
                    title: 'Archive Violation?',
                    text: 'This will move the violation to the archive list.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, archive it!',
                    cancelButtonText: 'Cancel'
                })
                .then((result) => {
                    if (result.isConfirmed) {
                        $.post('/admin/violation/archive', {
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
    });

    // 🟢 Load Archived Violations when modal is opened
    $('#archivedViolationModal').on('show.bs.modal', function() {
        $.get("{{ route('violations.archived') }}", function(res) {
            let tbody = $('#archivedTable tbody');
            tbody.html('<tr><td colspan="4" class="text-center">Loading...</td></tr>');

            $.get('{{ route("violations.archived") }}', function(res) {
                tbody.empty();

                if (res.violations && res.violations.length > 0) {
                    res.violations.forEach(function(v) {
                        tbody.append(`
            <tr>
                <td>${v.violation_id}</td>
                <td>${v.violation_type}</td>
                <td>₱${v.violation_amount}</td>
                <td>
                    <button class="btn btn-success btn-sm restore_violation" data-id="${v.violation_id}">
                        Restore
                    </button>

                        </td>
                    </tr>
                `);
                    });
                } else {
                    tbody.append(`<tr><td colspan="4" class="text-center">No archived violations found.</td></tr>`);
                }
            });
        });
    });

    // 🟢 Restore Violation
    $(document).on('click', '.restore_violation', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        const rowToRestore = $(this).closest('tr');

        Swal.fire({
            title: 'Restore Violation?',
            text: 'This will move the violation back to the active list.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, restore it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("{{ route('violations.restore') }}", {
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
                                <button class="btn btn-success btn-sm edit_data" data-id="${res.violation.violation_id}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-secondary btn-sm archive_data" data-id="${res.violation.violation_id}">
                                    <i class="fas fa-archive"></i>
                                </button>
                            </td>
                            <td>${res.violation.violation_id}</td>
                            <td>${res.violation.violation_type}</td>
                            <td>₱${res.violation.violation_amount}</td>
                        </tr>
                    `;

                        $('#dataTable tbody').append(newRow);
                    } else {
                        Swal.fire('Error', res.error || 'Restore failed.', 'error');
                    }
                }).fail(() => {
                    Swal.fire('Error', 'Could not restore violation.', 'error');
                });
            }
        });
    });


    $(document).ready(function() {
        $('#addViolationForm').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: "{{ route('violation.store') }}",
                method: "POST",
                data: $(this).serialize(),
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Added!',
                        text: res.success,
                        toast: true,
                        position: 'top-end',
                        timer: 3000,
                        showConfirmButton: false
                    });

                    // Append new violation to table
                    $('#dataTable tbody').append(`
                    <tr>
                        <td>
                            <button class="btn btn-success btn-xs edit_data" data-id="${response.violation.violation_id}">
                                <i class="fas fa-pen"></i>
                            </button>
                            <button class="btn btn-danger btn-xs delete_data" data-id="${response.violation.violation_id}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                        <td>${response.violation.violation_id}</td>
                        <td>${response.violation.violation_type}</td>
                        <td>₱${response.violation.violation_amount}</td>
                    </tr>
                `);

                    // Reset form
                    $('#addViolationForm')[0].reset();
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let errorMessage = '';
                        $.each(errors, function(key, value) {
                            errorMessage += value[0] + '<br>';
                        });

                        Swal.fire({
                            title: 'Error!',
                            html: errorMessage,
                            icon: 'error'
                        });
                    }
                }
            });
        });
    });
</script>


@endsection