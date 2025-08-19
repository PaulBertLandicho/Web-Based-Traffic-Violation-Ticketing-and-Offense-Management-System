<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Violation Ticket PDF</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 30px;
            color: #333;
        }

        .title {
            text-align: center;
            color: #0d6efd;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .subtitle {
            text-align: center;
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 20px;
        }

        .card {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 10px;
            max-width: 700px;
            margin: 0 auto;
            background-color: #fff;
        }

        .field {
            margin-bottom: 15px;
        }

        .field strong {
            color: #000;
            display: inline-block;
            width: 180px;
        }

        .status-paid {
            color: green;
            font-weight: bold;
        }

        .status-pending {
            color: red;
            font-weight: bold;
        }

        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 12px;
            color: #aaa;
        }
    </style>
</head>

<body>
    <div class="title">Traffic Violation Ticket</div>
    <div class="subtitle">View detailed information about your violation</div>

    <div class="card">
        <div class="field"><strong>Driver Name:</strong> {{ $ticket->driver_name }}</div>
        <div class="field"><strong>License ID:</strong> {{ $ticket->license_id }}</div>
        <div class="field"><strong>Vehicle No. & Type:</strong> {{ $ticket->vehicle_no }} ({{ $ticket->vehicle_type }})</div>
        <div class="field"><strong>Issued Date & Time:</strong> {{ $ticket->issued_date }} at {{ $ticket->issued_time }}</div>
        <div class="field"><strong>Issued Place:</strong> {{ $ticket->place }}</div>
        <div class="field"><strong>Violation Type:</strong> {{ $ticket->violation_type }}</div>
        <div class="field"><strong>Total Fine Amount:</strong> ₱{{ number_format($ticket->total_amount, 2) }}</div>
        @if($ticket->penalty_applied)
        <div class="field"><strong>Additional Penalty Notice:</strong> An additional penalty has been applied due to overdue payment.</div>
        @endif
        <div class="field"><strong>Status:</strong>
            @if($ticket->status === 'paid')
            <span class="status-paid">Paid</span>
            @else
            <span class="status-pending">Pending</span>
            @endif
        </div>
    </div>

    <div class="footer">
        This is a system-generated ticket. Please keep it for your records.
    </div>
</body>

</html>