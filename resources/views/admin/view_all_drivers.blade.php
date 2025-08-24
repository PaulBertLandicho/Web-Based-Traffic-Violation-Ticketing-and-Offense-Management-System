@extends('layouts.layout')
@section('title', 'View All Drivers | Traffic Administrative')

@section('content')
@include('layouts.components.admin.topNav')
@include('layouts.components.admin.leftsideNavbar')
@include('layouts.components.admin.modals.driver_details_modal')

<div class="dashwrapper">
    <div class="container-fluid">
        <h1 class="mt-4">View All Drivers Record</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('admin.admin-dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">View All Drivers</li>
        </ol>

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
                                    <button class="btn btn-danger btn-sm delete_data" data-id="{{ $driver->license_id }}"><i class="fas fa-trash-alt"></i></button>
                                </td>
                                <td>{{ $driver->license_id }}</td>
                                <td>{{ $driver->license_type }}</td>
                                <td>{{ $driver->driver_name }}</td>
                                <td>{{ $driver->license_issue_date }}</td>
                                <td>{{ $driver->license_expire_date }}</td>
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
    });

    $(document).ready(function() {
        $('#dataTable').DataTable();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        // ✅ View
        $('.view_data').click(function() {
            const id = $(this).data('id');
            $.post('/admin/driver/details', {
                did: id
            }, function(res) {
                $('#driver_detail').html(`
                    <strong>Name:</strong> ${res.driver.driver_name}<br>
                    <strong>License ID:</strong> ${res.driver.license_id}<br>
                    <strong>Address:</strong> ${res.driver.home_address || 'N/A'}<br>
                    <strong>Date of Birth:</strong> ${res.driver.date_of_birth || 'N/A'}<br>
                    <strong>License Type:</strong> ${res.driver.license_type || 'N/A'}<br>
                    <strong>Contact No:</strong> ${res.driver.contact_no || 'N/A'}<br>
                    <hr><strong>Violations Details:</strong><br>
                    ${res.violations.length ? res.violations.map(v => `${v.violation_type}, ₱${v.total_amount}, ${v.status}<br>`).join('') : 'No Violations'}
                `);
                $('#dataModal').modal('show');
            });
        });

        // ✅ Edit
        $('.edit_data').click(function() {
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


        // ✅ Delete
        $('.delete_data').click(function() {
            const id = $(this).data('id');
            if (!id) {
                return Swal.fire("Error", "Invalid driver ID.", "error");
            }

            Swal.fire({
                title: 'Are you sure?',
                text: 'This will permanently delete the driver.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e3342f',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/admin/driver/delete',
                        type: 'POST',
                        data: {
                            did: id
                        },
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            Swal.fire('Deleted!', res.success, 'success');

                            // ✅ Remove row without reload
                            let table = $('#dataTable').DataTable();
                            table.row($(`button[data-id="${id}"]`).parents('tr')).remove().draw();
                        },
                        error: function(xhr) {
                            if (xhr.status === 400 && xhr.responseJSON?.error) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Cannot Delete Driver',
                                    text: xhr.responseJSON.error
                                });
                            } else {
                                Swal.fire('Error', 'Delete failed due to server error.', 'error');
                            }
                        }
                    });
                }
            });
        });
    });
</script>

@endsection