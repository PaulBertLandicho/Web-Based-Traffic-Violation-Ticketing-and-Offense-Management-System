@extends('layouts.layout')

@section('content')
<div class="container py-5">
    <div class="text-center mb-4">
        <h2 class="fw-bold text-primary">Traffic Violation Ticket</h2>
        <p class="text-muted">View detailed information about your violation</p>
    </div>

    <div class="card shadow-lg border-0 rounded-4 p-4 mx-auto" style="max-width: 700px;">
        <div class="mb-3">
            <h5 class="text-dark"><i class="bi bi-file-earmark-ear me-2"></i><strong>Citation Ticket #:</strong>
                <span class="mb-0 ps-4">{{ $ticket->ref_no }}</span>
            </h5>
        </div>

        <div class="mb-3">
            <h5 class="text-dark"><i class="bi bi-person-circle me-2"></i><strong> Driver Name:</strong></h5>
            <p class="mb-0 ps-4">{{ $ticket->driver_name }}</p>
        </div>

        <div class="mb-3">
            <h5 class="text-dark"><i class="bi bi-card-heading me-2"></i><strong> License ID:</strong></h5>
            <p class="mb-0 ps-4">{{ $ticket->license_id }}</p>
        </div>

        <div class="mb-3">
            <h5 class="text-dark"><i class="bi bi-car-front-fill me-2"></i><strong> Vehicle No. & Type:</strong></h5>
            <p class="mb-0 ps-4">{{ $ticket->vehicle_no }} <span class="text-muted">({{ $ticket->vehicle_type }})</span></p>
        </div>

        <div class="mb-3">
            <h5 class="text-dark"><i class="bi bi-calendar-event-fill me-2"></i><strong> Issued Date & Time:</strong></h5>
            <p class="mb-0 ps-4">{{ $ticket->issued_date }} at {{ $ticket->issued_time }}</p>
        </div>

        <div class="mb-3">
            <h5 class="text-dark"><i class="bi bi-geo-alt-fill me-2"></i><strong> Issued Place:</strong></h5>
            <p class="mb-0 ps-4">{{ $ticket->place }}</p>
        </div>

        <div class="mb-3">
            <h5 class="text-dark"><i class="bi bi-exclamation-triangle-fill me-2 text-danger"></i><strong> Violation Type:</strong></h5>
            <p class="mb-0 ps-4">{{ $ticket->violation_type }}</p>
        </div>

        <div class="mb-3">
            <h5 class="text-dark"><i class="bi bi-cash-coin me-2"></i><strong> Total Fine Amount:</strong></h5>
            <p class="mb-0 ps-4 text-danger fw-semibold"> ₱{{ number_format($ticket->total_amount, 2) }}</p>
        </div>
        @if($ticket->penalty_applied)
        <div class="mb-3">
            <h5 class="text-dark">
                <i class="bi bi-exclamation-diamond-fill me-2 text-warning"></i>
                <strong> Additional Penalty Notice:</strong>
            </h5>
            <p class="mb-0 ps-4 text-warning">
                An additional penalty has been applied due to overdue payment.
            </p>
        </div>
        @endif
        <div class="mb-3">
            <h5 class="text-dark"><i class="bi bi-info-circle-fill me-2"></i><strong> Violation Status:</strong></h5>
            <p class="mb-0 ps-4">
                @if($ticket->status === 'paid')
                <span class="badge bg-success px-3 py-2">Paid</span>
                @else
                <span class="badge bg-danger px-3 py-2">Pending</span>
                @endif
            </p>
        </div>
    </div>
    <div class="text-center mt-4">
        <button onclick="window.print()" class="btn btn-outline-primary me-2">
            <i class="bi bi-printer-fill me-1"></i> Print Ticket
        </button>

        <a href="{{ route('download.ticket.pdf', $ticket->ref_no) }}" class="btn btn-outline-success">
            <i class="bi bi-file-earmark-arrow-down-fill me-1"></i> Download PDF
        </a>
    </div>
</div>
@endsection