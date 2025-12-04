@extends('layouts.layout')
@section('title', 'Paid Fine Tickets | Traffic Administrative')

@section('content')
@include('layouts.components.admin.topNav')
@include('layouts.components.admin.leftsideNavbar')
@include('layouts.components.footer')

<div class="dashwrapper">
    <div class="container-fluid">
        <h1 class="mt-4">List of Paid Fine Tickets</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="">Dashboard</a></li>
            <li class="breadcrumb-item active">Paid Fine Tickets</li>
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
                            @foreach ($paidTickets as $ticket)
                            <tr>
                                <td>
                                    <button class="btn btn-secondary view-btn" data-id="{{ $ticket->ref_no }}">
                                        <i class="fas fa-ticket-alt text-warning"></i> Tickets
                                    </button>
                                </td>
                                <td>{{ $ticket->ref_no }}</td>
                                <td>{{ $ticket->enforcer_id }}</td>
                                <td>{{ $ticket->license_id }}</td>
                                <td>{{ $ticket->driver_name }}</td>
                                <td>₱{{ number_format($ticket->total_amount, 2) }}</td>
                                <td><span class="badge badge-success">Paid</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ✅ Modal for Viewing Ticket -->
<div class="modal fade" id="paidTicketModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h4 class="modal-title" id="ticketDetailsLabel">
                    <img src="../assets/img/ICTPMO-logo.png" style="width: 40px; height: 40px; margin-right: 10px;">
                    Paid Ticket Details
                </h4>
                <button class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body" id="paidTicketContent">Loading...</div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Close
                </button>
                <div>
                    <button type="button" class="btn btn-danger me-2" id="pdfTicketBtn">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
                    </button>
                    <!-- <button type="button" class="btn btn-outline-success" onclick="printFineDetails()">
                        <i class="bi bi-printer me-1"></i> Print
                    </button> -->
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <!-- ✅ Tooltip & DataTable Initialization -->
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


        // View ticket modal
        $('.view-btn').on('click', function() {
            const refNo = $(this).data('id');

            fetch('{{ route("admin.paidTickets.details") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        ref_no: refNo
                    })
                })
                .then(res => res.text())
                .then(html => {
                    $('#paidTicketContent').html(html);
                    $('#paidTicketModal').modal('show');
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire('Error', 'Failed to load ticket details.', 'error');
                });
        });
    </script>
    <script>
        // Start Printable Citation Ticket
        function printFineDetails() {
            // Get the content of the fine details modal
            var fineContent = document.getElementById("paidTicketContent").innerHTML;

            // Correct logo URL
            var logoUrl = window.location.origin + "/assets/img/ICTPMO-logo.png";

            // Header for print
            var headerContent = `
        <div style="text-align: center; margin-bottom: 20px;">
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
        $(document).on('click', '#pdfTicketBtn', function() {
            const {
                jsPDF
            } = window.jspdf;
            const doc = new jsPDF('p', 'pt', 'a4'); // Portrait, points, A4

            // Ensure modal content is fully loaded
            var content = document.getElementById('paidTicketContent');

            // Convert relative image URLs to absolute
            const imgs = content.querySelectorAll('img');
            imgs.forEach(img => {
                if (!img.src.startsWith('http')) {
                    img.src = window.location.origin + img.getAttribute('src');
                }
            });

            // Generate PDF
            doc.html(content, {
                callback: function(pdf) {
                    // Get citation number for filename
                    var citationNoElem = content.querySelector('p strong');
                    var citationNo = citationNoElem ? citationNoElem.innerText.replace('#', '') : 'CitationTicket';

                    pdf.save('Traffic_Citation_Ticket_' + citationNo + '.pdf');
                },
                x: 20,
                y: 20,
                width: 555, // Adjust width for A4
                windowWidth: content.scrollWidth,
                html2canvas: {
                    scale: 0.8, // Fit content to page
                    useCORS: true,
                    allowTaint: true
                }
            });
        });

        // End Printable Citation Ticket
    </script>
    @endsection