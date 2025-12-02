@extends('layouts.layout')
@section('title', 'User Activity Logs | Traffic Administrative')

@section('content')
@include('layouts.components.admin.topNav')
@include('layouts.components.admin.leftsideNavbar')
@include('layouts.components.footer')

<div class="dashwrapper">
    <div class="container-fluid">
        <h1 class="mt-4">User Activity Logs</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="">Dashboard</a></li>
            <li class="breadcrumb-item active">View All User Activity Logs</li>
        </ol>

        <div class="card mt-3">
            <div class="card-header">
                <i class="fas fa-table"></i> User Logs for Auditing
            </div>
            <div class="card-body">
                <div class="table-responsive" id="employee_table">
                    <table class="table table-striped table-bordered" id="dataTable">
                        <thead>
                            <tr>
                                <th>User Name</th>
                                <th>Action</th>
                                <th>Details</th>
                                <th>IP Address</th>
                                <th>Date & Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Enforcer Logs --}}
                            @forelse($userLogs as $log)
                            <tr>
                                <td>
                                    <span class="badge badge-primary">Enforcer</span><br>
                                    {{ $log->enforcer->enforcer_id ?? 'Unknown ID' }} -
                                    {{ $log->enforcer->enforcer_name ?? 'Unknown Enforcer' }}
                                </td>
                                <td>{{ $log->action }}</td>
                                <td>{{ $log->details ?? '-' }}</td>
                                <td>{{ $log->ip_address }}</td>
                                <td>{{ $log->created_at->timezone('Asia/Manila')->format('M d, Y - h:i A') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">No enforcer logs found.</td>
                            </tr>
                            @endforelse

                            {{-- Admin Logs --}}
                            @forelse($adminLogs as $log)
                            <tr>
                                <td>
                                    <span class="badge badge-danger">Admin</span><br>
                                    {{ $log->admin->admin_name ?? 'Admin ID: '.$log->admin_id }}
                                </td>
                                <td>{{ $log->action }}</td>
                                <td>{{ $log->user_agent ?? '-' }}</td>
                                <td>{{ $log->ip_address }}</td>
                                <td>{{ \Carbon\Carbon::parse($log->created_at)->timezone('Asia/Manila')->format('M d, Y - h:i A') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">No admin logs found.</td>
                            </tr>
                            @endforelse
                        </tbody>

                    </table>
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
                    sSearchPlaceholder: "Search..."
                },
                initComplete: function() {

                    // ----------------------------
                    // 💡 STYLE SEARCH BAR
                    // ----------------------------
                    const $filter = $('.dataTables_filter');
                    $filter.addClass('d-flex align-items-center gap-2');

                    const $input = $filter.find('input');
                    $input
                        .attr('placeholder', 'Search...')
                        .addClass('form-control')
                        .css({
                            'padding-left': '30px',
                            'width': '200px'
                        });

                    $filter.find('label').prepend('<i class="fas fa-search search-icon mr-2"></i>');

                    // ----------------------------
                    // ⭐ ADD ROLE FILTER BESIDE SEARCH BAR
                    // ----------------------------
                    var roleDropdown = `
                    <label class="ml-2 mb-2">
                    <select id="roleFilter" class="form-control form-control-sm">
                    <option value="">Filter by </option>
                    <option value="Admin">Admin</option>
                    <option value="Enforcer">Enforcer</option>
                    </select>
                </label>
            `;

                    $filter.append(roleDropdown);
                }
            });

            // ----------------------------
            // 🔍 ROLE FILTER FUNCTION
            // ----------------------------
            $(document).on('change', '#roleFilter', function() {
                table.column(0).search($(this).val()).draw();
            });

        });
    </script>
    @endsection