@extends('layouts.layout')
@section('title', 'Manage Notifications | Traffic Administrative')

@section('content')
@include('layouts.components.admin.topNav')
@include('layouts.components.admin.leftsideNavbar')

<div class="dashwrapper">
    <div class="container-fluid">
        <h1 class="mt-4">Manage Notifications</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('admin.admin-dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Notifications</li>
        </ol>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>

                <button class="btn btn-warning" data-toggle="modal" data-target="#sendNoticeModal">
                    <i class="fas fa-bell"></i> Send Notice / Reminder
                </button>
            </div>
        </div>

        <!-- Send Notice Modal -->
        <div class="modal fade" id="sendNoticeModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('enforcer.sendNotice') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Send Notice / Reminder</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Message</label>
                                <textarea name="message" class="form-control" required></textarea>
                            </div>
                            <div class="form-group">
                                <label>Send To</label>
                                <select name="enforcer_id" class="form-control">
                                    <option value="all">All Enforcers</option>
                                    @foreach($enforcers as $enforcer)
                                    <option value="{{ $enforcer->enforcer_id }}">({{ $enforcer->enforcer_id }}) - {{ $enforcer->enforcer_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Send</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card mt-3">
            <div class="card-header">
                <i class="fas fa-bell"></i> Sent Notifications
            </div>
            <div class="card-body">
                <table class="table table-striped table-bordered" id="dataTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Enforcer</th>
                            <th>Title</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Sent At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Empty – will be filled by AJAX --}}
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

<!-- ✅ Tooltip + DataTables Initialization -->
<script>
    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();

        // ✅ CSRF token for forms
        const csrf = '{{ csrf_token() }}';

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
            ],
            ajax: {
                url: "{{ route('notifications.ajax') }}", // ✅ must return JSON
                dataSrc: ""
            },
            columns: [{
                    data: 'id'
                },
                {
                    data: 'enforcer_name',
                    render: function(data) {
                        return data ? data : '<span class="badge badge-info">All Enforcers</span>';
                    }
                },
                {
                    data: 'title'
                },
                {
                    data: 'message'
                },
                {
                    data: 'is_read',
                    render: function(data) {
                        return `<span class="badge ${data ? 'badge-success' : 'badge-warning'}">
                            ${data ? 'Read' : 'Unread'}
                        </span>`;
                    }
                },
                {
                    data: 'created_at'
                },
                {
                    data: 'id',
                    render: function(data) {
                        return `
                            <form action="/notifications/${data}" method="POST" onsubmit="return confirm('Delete this notification?')">
                                <input type="hidden" name="_token" value="${csrf}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                            </form>`;
                    }
                }
            ]
        });

        // 🔄 Auto-refresh every 5 seconds
        setInterval(() => {
            table.ajax.reload(null, false); // false = keep current page
        }, 5000);
    });
</script>

@endsection