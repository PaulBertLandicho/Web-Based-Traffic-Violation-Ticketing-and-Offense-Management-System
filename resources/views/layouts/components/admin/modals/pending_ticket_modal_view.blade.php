<div class="text-center mb-4">
    <img src="/assets/img/ICTPMO-logo.png" alt="ICTPMO Logo" width="90" height="90" style="margin-bottom: 10px;">
    <h5 class="text-center mb-3">TRAFFIC CITATION TICKET</h5>
    <p class="text-center">Citation No: <strong>#{{ $ticket->ref_no }}</strong></p>
</div>
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
        <td>
            {{ \Carbon\Carbon::parse($ticket->issued_date)->format('M. d, Y') }} - {{ $ticket->issued_time }}
        </td>
    </tr>
    <tr>
        <td>Expiration Date:</td>
        <td>{{ \Carbon\Carbon::parse($ticket->expire_date)->format('M. d, Y') }}</td>
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
        <td><strong>PHP{{ number_format($ticket->total_amount, 2) }}</strong></td>
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
<div class="row mt-4 text-center">
    <div class="col">
        <p>Traffic Enforcer Signature</p>
        @if(!empty($ticket->enforcer_signature))
        <img src="{{ asset($ticket->enforcer_signature) }}" alt="Enforcer Signature" style="height: 80px;">
        @else
        <p>_________________________</p>
        @endif
        <p>{{ $ticket->enforcer_name }}</p>
    </div>
    <div class="col">
        <p>Driver Signature</p>
        @if(!empty($ticket->driver_signature))
        <img src="{{ asset($ticket->driver_signature) }}" alt="Driver Signature" style="height: 80px;">
        @else
        <p>_________________________</p>
        @endif
        <p>{{ $ticket->driver_name }}</p>
    </div>
</div>