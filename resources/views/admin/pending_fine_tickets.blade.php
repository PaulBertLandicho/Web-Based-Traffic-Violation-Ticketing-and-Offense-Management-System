@extends('layouts.layout')
@section('title', 'Pending Fine Tickets | Traffic Administrative')

@section('content')
@include('layouts.components.admin.topNav')
@include('layouts.components.admin.leftsideNavbar')

<div class="dashwrapper">
    <div class="container-fluid">
        <h1 class="mt-4">List of Pending Fine Tickets</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="">Dashboard</a></li>
            <li class="breadcrumb-item active">Pending Fine Tickets</li>
        </ol>

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
                                <th>Reference No.</th>
                                <th>Enforcer ID</th>
                                <th>License ID</th>
                                <th>Driver Name</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pendingTickets as $ticket)
                            @php
                            $isExpired = \Carbon\Carbon::now()->gt($ticket->expire_date);
                            @endphp
                            <tr>
                                <td>
                                    <button class="btn btn-secondary view-btn" data-id="{{ $ticket->ref_no }}">
                                        <i class="fas fa-ticket-alt text-warning"></i> Tickets
                                    </button>
                                    <button class="btn btn-warning pay-btn" data-id="{{ $ticket->ref_no }}">
                                        Paid Now <i class="fas fa-coins"></i>
                                    </button>
                                </td>
                                <td>
                                    {{ $ticket->ref_no }}
                                    @if(\Carbon\Carbon::parse($ticket->created_at)->isToday())
                                    <span class="badge bg-success">New Issued</span>
                                    @endif
                                </td>
                                <td>{{ $ticket->enforcer_id }}</td>
                                <td>{{ $ticket->license_id }}</td>
                                <td>{{ $ticket->driver_name }}</td>
                                <td>₱{{ number_format($ticket->total_amount, 2) }}</td>
                                <td>
                                    @if ($isExpired)
                                    <span class="badge badge-danger judicial-badge"
                                        data-refno="{{ $ticket->ref_no }}"
                                        data-driver="{{ $ticket->driver_name }}"
                                        data-license="{{ $ticket->license_id }}"
                                        data-violation="{{ $ticket->violation_type }}"
                                        data-issued="{{ $ticket->issued_date }}"
                                        data-expire="{{ $ticket->expire_date }}"
                                        data-amount="{{ $ticket->total_amount }}">
                                        <i class="fas fa-exclamation-triangle"></i> Due Date
                                    </span>
                                    @else
                                    <span class="badge badge-warning">Pending</span>
                                    @endif
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

<!-- ===================== Judicial Form Modal ===================== -->
<div class="modal fade" id="judicialFormModal" tabindex="-1" aria-labelledby="judicialFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4">
            <div class="modal-header bg-primary text-white rounded-top-4">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-file-earmark-text me-2"></i> ICTPMO Judicial Violation Report
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="judicialFormContent">
                <!-- Content filled dynamically by JS -->
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Close
                </button>
                <div>
                    <button type="button" class="btn btn-outline-success me-2" id="printJudicialForm">
                        <i class="bi bi-printer me-1"></i> Print
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* 🌐 Modern Glass Effect for Modal */
    .modal-content {
        backdrop-filter: blur(10px);
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid rgba(0, 0, 0, 0.1);
    }

    /* 🖨️ Print Styling: Actual Judicial Form look */
    @media print {
        body * {
            visibility: hidden;
        }

        #printableJudicialForm,
        #printableJudicialForm * {
            visibility: visible;
        }

        #printableJudicialForm {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            font-family: "Times New Roman", serif;
            color: black !important;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }
    }
</style>

<!-- Modal -->
<div class="modal fade" id="ticketModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h4 class="modal-title" id="ticketDetailsLabel">
                    <img src="../assets/img/ICTPMO-logo.png" style="width: 40px; height: 40px; margin-right: 10px;">
                    Pending Ticket Details
                </h4>
                <button class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body" id="ticketDetailContent">
                Loading...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="printFineDetails()">
                    <i class="fas fa-print"></i> Print
                </button>
                <div>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
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

                // ✅ Initialize DataTable and store reference
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

                        // ✅ Add dropdown filter beside search
                        $filter.append(`
                <label class="ml-3 mb-0">
                    <select id="statusFilter" class="form-control form-control-sm">
                        <option value="">Filter by Status</option>
                        <option value="Pending">Pending</option>
                        <option value="Due Date">Due Date</option>
                    </select>
                </label>
            `);

                        // ✅ Apply filter on change
                        $('#statusFilter').on('change', function() {
                            const selected = $(this).val();
                            api.column(6) // 👉 change index if your "Status" column is different
                                .search(selected ? '^' + selected + '$' : '', true, false)
                                .draw();
                        });
                    }
                });


                // ✅ View Ticket (Dynamic Modal Load)
                $(document).on('click', '.view-btn', function() {
                    const refNo = $(this).data('id');

                    fetch('{{ route("admin.pendingTickets.details") }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                did: refNo
                            })
                        })
                        .then(res => res.text())
                        .then(html => {
                            $('#ticketDetailContent').html(html);
                            $('#ticketModal').modal('show');
                        })
                        .catch(() => {
                            Swal.fire('Error', 'Failed to load ticket details.', 'error');
                        });
                });

                // ✅ Pay Ticket (No Reload, Remove Row from Table)
                $(document).on('click', '.pay-btn', function() {
                    const refNo = $(this).data('id');
                    const row = $(this).closest('tr'); // store row for removal later

                    Swal.fire({
                        title: 'Mark as Paid?',
                        text: 'When the driver is already paid.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Paid it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch('{{ route("admin.pendingTickets.pay") }}', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        ref_no: refNo
                                    })
                                })
                                .then(res => res.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire('Paid!', data.message, 'success');

                                        // ✅ Remove row from DataTable without reload
                                        table.row(row).remove().draw();
                                    } else {
                                        Swal.fire('Error', 'Could not update.', 'error');
                                    }
                                })
                                .catch(() => {
                                    Swal.fire('Error', 'Request failed.', 'error');
                                });
                        }
                    });
                });
            });
        </script>
        <script>
            // Start Printable Citation Ticket
            function printFineDetails() {
                // Get the content of the fine details modal
                var fineContent = document.getElementById("ticketDetailContent").innerHTML;

                // Correct logo URL
                var logoUrl = window.location.origin + "/assets/img/ICTPMO-logo.png";

                // Header for print
                var headerContent = `
        <div style="text-align: center; margin-bottom: 20px;">
            <img src="${logoUrl}" style="width: 70px; height: 70px; margin-bottom: 5px;">
            <h2 style="margin-top: 5px;">Fine Ticket Details</h2>
        </div>
    `;

                // Open print window
                var printWindow = window.open('', '', 'height=700,width=900');
                printWindow.document.write('<html><head><title>Fine Ticket Details</title>');
                printWindow.document.write('<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">');

                // Add print-optimized CSS
                printWindow.document.write(`
        <style>
            @media print {
                @page {
                    size: A4 portrait;
                    margin: 10mm;
                }

                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 0;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                    zoom: 95%; /* Slight scale down to help fit on one page */
                }

                table {
                    width: 100%;
                    border-collapse: collapse;
                    page-break-inside: avoid;
                }

                th, td {
                    padding: 6px 10px;
                    vertical-align: top;
                    font-size: 14px;
                }

                th {
                    width: 35%;
                    font-weight: bold;
                    text-align: right;
                    white-space: nowrap;
                }

                td {
                    width: 65%;
                    text-align: left;
                }

                h5, h6 {
                    margin-top: 20px;
                    margin-bottom: 10px;
                }

                img {
                    max-width: 70px;
                    height: auto;
                }

                .row {
                    display: flex;
                    justify-content: space-between;
                    margin-top: 30px;
                }

                .col {
                    width: 48%;
                    text-align: center;
                }

                hr {
                    width: 150px;
                    margin: 0 auto 5px;
                    border: 1px solid #000;
                }

                .badge {
                    padding: 4px 8px;
                    font-size: 12px;
                }

                .badge-warning {
                    background-color: #ffc107 !important;
                    color: #000;
                }

                .text-center {
                    text-align: center;
                }

                p {
                    font-size: 13px;
                }
            }
        </style>
    `);

                printWindow.document.write('</head><body>');
                printWindow.document.write(headerContent);
                printWindow.document.write('<div class="print-container">' + fineContent + '</div>');
                printWindow.document.write('</body></html>');

                printWindow.document.close();
                printWindow.focus();
                printWindow.print();
                printWindow.close();
            }
            // End Printable Citation Ticket

            function loadPendingTickets() {
                $.ajax({
                    url: "{{ route('admin.pendingTickets.fetch') }}",
                    method: "GET",
                    success: function(data) {
                        let today = new Date().toISOString().slice(0, 10);
                        let rows = ""; // initialize rows

                        // Separate today's issued and older tickets
                        let todaysTickets = [];
                        let olderTickets = [];

                        data.forEach(ticket => {
                            if (ticket.issued_date === today) {
                                todaysTickets.push(ticket);
                            } else {
                                olderTickets.push(ticket);
                            }
                        });

                        // Function to generate row HTML
                        function generateRow(ticket) {
                            let newIssuedBadge = (ticket.issued_date === today) ?
                                '<span class="badge bg-success mr-1">New Issued</span>' :
                                '';

                            return `
        <tr>
            <td>
                <button class="btn btn-secondary view-btn" data-id="${ticket.ref_no}">
                    <i class="fas fa-ticket-alt text-warning"></i> Tickets
                </button>
                <button class="btn btn-warning pay-btn" data-id="${ticket.ref_no}">
                    Paid Now <i class="fas fa-coins"></i>
                </button>
            </td>
            <td>${newIssuedBadge} ${ticket.ref_no}</td>
            <td>${ticket.enforcer_id}</td>
            <td>${ticket.license_id}</td>
            <td>${ticket.driver_name}</td>
            <td>${ticket.formatted_amount}</td>
            <td>
                ${ticket.isExpired 
                    ? '<span class="badge badge-danger">Due Date</span>' 
                    : '<span class="badge badge-warning">Pending</span>'
                }
            </td>
        </tr>`;
                        }

                        // Build rows with today's tickets first
                        todaysTickets.forEach(ticket => {
                            rows += generateRow(ticket);
                        });

                        olderTickets.forEach(ticket => {
                            rows += generateRow(ticket);
                        });

                        // Render into table
                        $("#dataTable tbody").html(rows);


                        // 🔄 Reapply current filter
                        let selected = $('#statusFilter').val();
                        $('#dataTable').DataTable().column(6).search(selected, true, false).draw();
                    }
                });
            }


            // Initial load
            loadPendingTickets();

            // Refresh every 5 seconds
            setInterval(loadPendingTickets, 5000);
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const badges = document.querySelectorAll('.judicial-badge');

                badges.forEach(badge => {
                    badge.addEventListener('click', function() {
                        const refNo = this.dataset.refno;
                        const driver = this.dataset.driver;
                        const license = this.dataset.license;
                        const violation = this.dataset.violation;
                        const issued = this.dataset.issued;
                        const expire = this.dataset.expire;
                        const amount = this.dataset.amount;

                        const content = `
                <div id="printableJudicialForm" class="p-4">
                    <div class="text-center mb-4">
                        <img src="/assets/img/ICTPMO-logo.png" alt="ICTPMO Logo" width="90" height="90" style="margin-bottom: 10px;">
                        <h4 class="fw-bold text-uppercase">City Government of Iligan</h4>
                        <h5 class="fw-bold">Traffic and Parking Management Office (ICTPMO)</h5>
                        <p class="text-muted"><em>Judicial Traffic Violation Report Form</em></p>
                    </div>

                    <hr>

                    <h6 class="fw-bold text-primary">Driver Information</h6>
                    <table class="table table-bordered table-sm mt-2">
                        <tr><th>Full Name</th><td>${driver}</td></tr>
                        <tr><th>License ID</th><td>${license}</td></tr>
                    </table>

                    <h6 class="fw-bold text-primary mt-3">Violation Details</h6>
                    <table class="table table-bordered table-sm mt-2">
                        <tr><th>Reference No.</th><td>${refNo}</td></tr>
                        <tr><th>Violation</th><td>${violation}</td></tr>
                        <tr><th>Date Issued</th><td>${issued}</td></tr>
                        <tr><th>Due Date</th><td>${expire}</td></tr>
                        <tr><th>Total Fine</th><td>₱${parseFloat(amount).toFixed(2)}</td></tr>
                        <tr><th>Status</th><td><span class="text-danger fw-bold">UNPAID / FOR JUDICIAL ACTION</span></td></tr>
                    </table>

                    <h6 class="fw-bold text-primary mt-3">Judicial Action Summary</h6>
                    <p class="mt-2">
                        This violation has reached its due date and remains unpaid. Under the City Traffic Enforcement Ordinance,
                        this report is hereby endorsed by the <strong>ICTPMO - Iligan City Traffic and Parking Management Office</strong>
                        for legal action to the <strong>Municipal Trial Court of Iligan City</strong>.
                        The concerned driver is required to appear before the court and comply with judicial proceedings
                        to resolve the violation and corresponding penalties.
                    </p>

                    <div class="row mt-5 text-center">
                        <div class="col">
                            <p>Traffic Enforcer Signature</p>
                            <div style="height: 10px;"></div>
                            <p>_________________________</p>
                        </div>
                        <div class="col">
                            <p>Authorized ICTPMO Officer</p>
                            <div style="height: 10px;"></div>
                            <p>_________________________</p>
                        </div>
                    </div>

                    <p class="text-center mt-4">
                        <em>Generated via ICTPMO Traffic Violation Management System</em><br>
                        <small>Generated on: ${new Date().toLocaleString()}</small>
                    </p>
                </div>
            `;

                        document.getElementById('judicialFormContent').innerHTML = content;
                        const modal = new bootstrap.Modal(document.getElementById('judicialFormModal'));
                        modal.show();
                    });
                });

                // 🖨️ Print Judicial Form
                document.getElementById('printJudicialForm').addEventListener('click', function() {
                    const printContents = document.getElementById('printableJudicialForm').innerHTML;
                    const printWindow = window.open('', '_blank');
                    printWindow.document.write(`
            <html>
                <head>
                    <title>ICTPMO Judicial Violation Report</title>
                    <style>
                        body {
                            font-family: "Times New Roman", serif;
                            color: black;
                            padding: 20px;
                        }
                        table {
                            width: 100%;
                            border-collapse: collapse;
                            margin-top: 10px;
                        }
                        table, th, td {
                            border: 1px solid black;
                            padding: 6px;
                        }
                        h4, h5, h6 {
                            text-align: center;
                            margin: 0;
                        }
                        p {
                            font-size: 14px;
                            text-align: justify;
                        }
                        img {
                            display: block;
                            margin: 0 auto;
                            width: 90px;
                            height: 90px;
                        }
                        hr {
                            border: 1px solid black;
                            margin: 20px 0;
                        }
                        @media print {
                            body { -webkit-print-color-adjust: exact; }
                        }
                    </style>
                </head>
                <body>${printContents}</body>
            </html>
        `);
                    printWindow.document.close();
                    printWindow.print();
                });
            });
        </script>

        @endsection