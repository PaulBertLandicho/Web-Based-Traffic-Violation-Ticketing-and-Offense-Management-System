@extends('layouts.layout')
@section('title', 'SMS Activity Logs | Traffic Administrative')

@section('content')
@include('layouts.components.admin.topNav')
@include('layouts.components.admin.leftsideNavbar')

<div class="dashwrapper">
    <div class="container-fluid">
        <h1 class="mt-4">SMS Activity Logs</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="">Dashboard</a></li>
            <li class="breadcrumb-item active">View All SMS Logs</li>
        </ol>

        <div class="card mt-5 mb-4">
            <div class="card-header">
                <i class="fas fa-table mr-1"></i> Recent SMS Notifications
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive" id="employee_table">
                <table class="table table-striped table-bordered" id="dataTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>License ID</th>
                            <th>Contact No.</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Response</th>
                            <th>Sent At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($smsLogs as $index => $log)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $log->license_id }}</td>
                            <td>{{ $log->contact_no }}</td>
                            <td>{!! nl2br(e($log->message)) !!}</td>
                            <td>
                                <span class="badge badge-{{ $log->sent_status === 'success' ? 'success' : 'success' }}">
                                    {{ $log->sent_status }}
                                </span>

                                @if ($log->sent_status === 'failed')
                                <form method="POST" action="{{ route('sms.resend') }}" class="d-inline ml-2">
                                    @csrf
                                    <input type="hidden" name="sms_id" value="{{ $log->id }}">
                                    <button type="submit" class="btn btn-sm btn-warning"
                                        onclick="return confirm('Resend this SMS?')">
                                        Resend
                                    </button>
                                </form>
                                @endif
                            </td>
                            <td>{{ $log->response }}</td>
                            <td>{{ $log->created_at }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
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
    </script>
    @endsection