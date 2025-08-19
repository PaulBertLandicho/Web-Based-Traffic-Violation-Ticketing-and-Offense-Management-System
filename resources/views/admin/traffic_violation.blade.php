@extends('layouts.layout')
@section('title', 'Add Traffic Violations | Traffic Administrative')

@section('content')
@include('layouts.components.admin.topNav')
@include('layouts.components.admin.leftsideNavbar')
@include('layouts.components.admin.modals.violation_details_modal')

<!-- Dashboard main content start here =================================================-->
<div class="dashwrapper animated fadeIn">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
        <div class="container-fluid">
            <h1 class="mt-4">Manage Violation Details</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item">Dashboard</li>
                <li class="breadcrumb-item active">Provisions Details</li>
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

            <!--Add fine tickets form includes goes here-->
            <div class="card mt-5 mb-4">
                <div class="card-header">
                    <i class="fas fa-receipt"></i> Add a Provision Details
                </div>
                <div class="card-body" style="margin:0 2rem 1rem 2rem;">
                    <form action="{{ route('violation.store') }}" method="POST">
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
                                        <button class="btn btn-success btn-xs edit_data" data-id="{{ $violation->violation_id }}"><i class="fas fa-pen"></i></button>
                                        <button class="btn btn-danger btn-xs delete_data" data-id="{{ $violation->violation_id }}"><i class="fas fa-trash-alt"></i></button>
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

            // =========================
            // EDIT HANDLER
            // =========================
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

            // =========================
            // UPDATE HANDLER (no reload)
            // =========================
            $('#edit_form').submit(function(e) {
                e.preventDefault();

                $.post('/admin/violation/update', $(this).serialize(), function(res) {
                    $('#edit_data_Modal').modal('hide');

                    // Update row data in DataTable
                    const updatedType = $('#violation_type').val();
                    const updatedAmount = $('#violation_amount').val();

                    table.row(currentRow).data([
                        currentRow.find('td').eq(0).html(), // Action buttons unchanged
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

            // =========================
            // DELETE HANDLER (no reload)
            // =========================
            $(document).on('click', '.delete_data', function() {
                const id = $(this).data('id');
                const rowToDelete = $(this).closest('tr');

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'This will permanently delete the violation.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e3342f',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post('/admin/violation/delete', {
                            did: id
                        }, function(res) {
                            // Remove row from DataTable
                            table.row(rowToDelete).remove().draw(false);

                            Swal.fire('Deleted!', res.success, 'success');
                        }).fail(() => {
                            Swal.fire('Error', 'Delete failed.', 'error');
                        });
                    }
                });
            });

        });
    </script>


    @endsection