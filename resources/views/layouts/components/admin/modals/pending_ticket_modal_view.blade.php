<h5 class="text-center mb-3">TRAFFIC CITATION TICKET</h5>
<p class="text-center">Citation No: <strong>#{{ $ticket->ref_no }}</strong></p>

<h6 class="border-bottom pb-1"><strong>Driver Information</strong></h6>
<table class="table table-sm table-borderless mb-2">
    <tr>
        <td>Full Name:</td>
        <td>{{ $ticket->driver_name }}</td>
    </tr>
    <tr>
        <td>License Number:</td>
        <td>{{ $ticket->license_id }}</td>
    </tr>
    <tr>
        <td>License Type:</td>
        <td>{{ $ticket->license_type }}</td>
    </tr>
    <tr>
        <td>Contact No:</td>
        <td>{{ $ticket->contact_no }}</td>
    </tr>
</table>

<h6 class="border-bottom pb-1"><strong>Vehicle Information</strong></h6>
<table class="table table-sm table-borderless mb-2">
    <tr>
        <td>Class of Vehicle:</td>
        <td>{{ $ticket->vehicle_type ?? 'N/A' }}</td>
    </tr>
    <tr>
        <td>Vehicle No.:</td>
        <td>{{ $ticket->vehicle_no }}</td>
    </tr>
</table>

<h6 class="border-bottom pb-1"><strong>Violation Details</strong></h6>
<table class="table table-sm table-borderless mb-2">
    <tr>
        <td>Issued Place:</td>
        <td>{{ $ticket->place }}</td>
    </tr>
    <tr>
        <td>Issued Date & Time:</td>
        <td>{{ $ticket->issued_date }} - {{ $ticket->issued_time }}</td>
    </tr>
    <tr>
        <td>Expiration Date:</td>
        <td>{{ $ticket->expire_date }}</td>
    </tr>
    <tr>
        <td>Provisions:</td>
        <td>
            @if (!empty($ticket->violation_type))
            @foreach (explode(',', $ticket->violation_type) as $violation_type)
            [ {{ trim($violation_type) }} ]<br>
            @endforeach
            @else
            [ ]
            @endif
        </td>
    </tr>
    <tr>
        <td>Total Amount:</td>
        <td><strong>₱{{ number_format($ticket->total_amount, 2) }}</strong></td>
    </tr>
    <tr>
        <td>Status:</td>
        <td>
            <span class="badge badge-{{ $ticket->status === 'paid' ? 'success' : 'warning' }}">
                {{ ucfirst($ticket->status) }}
            </span>
        </td>
    </tr>
</table>

<p class="text-center mt-4" style="font-size: 12px;">
    This citation is not an admission of guilt. Please settle within 7 days to avoid additional penalties.
</p>