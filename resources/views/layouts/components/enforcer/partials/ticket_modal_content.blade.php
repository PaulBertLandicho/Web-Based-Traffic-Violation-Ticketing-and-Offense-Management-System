<div class="modal-header bg-info text-white">
    <h4 class="modal-title" id="ticketDetailsLabel">
        <img src="../assets/img/ICTPMO-logo.png" style="width: 40px; height: 40px; margin-right: 10px;">
        Citation Tickets
    </h4>
    <a href="{{ route('fine.clearTicket') }}" class="btn btn-danger"><span>&times;</span></a>
</div>

<div class="modal-body" id="fine_detail">
    <div class="ticket px-3">
        <p class="text-center">Citation No: <strong># {{ $ticket->ref_no ?? 'N/A' }}</strong></p>

        {{-- Driver Info --}}
        <h4 class="border-bottom pb-1 mb-3" style="font-weight: bold;"><strong>Driver Information</strong></h4>
        <table class="table table-sm table-borderless mb-2">
            <tr>
                <td>Full Name:</td>
                <td>{{ $driver->driver_name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>License Number:</td>
                <td>{{ $driver->license_id ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>License Type:</td>
                <td>{{ $driver->license_type ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Address:</td>
                <td>{{ $driver->home_address ?? 'N/A' }}</td>
            </tr>
        </table>
        <br>
        {{-- Vehicle Info --}}
        <h4 class="border-bottom pb-1 mb-3" style="font-weight: bold;"><strong>Vehicle Information</strong></h4>
        <table class="table table-sm table-borderless mb-2">
            <tr>
                <td>Vehicle Number:</td>
                <td>{{ $ticket->vehicle_no ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Vehicle Make:</td>
                <td>{{ $ticket->vehicle_make ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Vehicle Model:</td>
                <td>{{ $ticket->vehicle_model ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Vehicle Color:</td>
                <td>{{ $ticket->vehicle_color ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Vehicle Type:</td>
                <td>{{ $ticket->vehicle_type ?? 'N/A' }}</td>
            </tr>
        </table>
        <br>
        {{-- Violation Details --}}
        <h4 class="border-bottom pb-1 mb-3" style="font-weight: bold;"><strong>Violation Details</strong></h4>
        <table class="table table-sm table-borderless mb-2">
            <tr>
                <td>Issued Place:</td>
                <td>{{ $ticket->place }}</td>
            </tr>
            <tr>
                <td>Issued Date & Time:</td>
                <td>{{ $ticket->issued_date ?? '' }} {{ $ticket->issued_time ?? '' }}</td>
            </tr>
            <tr>
                <td>Expiration Date:</td>
                <td>{{ $ticket->expire_date }}</td>
            </tr>
        </table>

        <p style="font-weight: bold;"><strong>Offense Number:</strong>
            @if($ticket->offense_number == 1) 1st Offense
            @elseif($ticket->offense_number == 2) 2nd Offense
            @elseif($ticket->offense_number == 3) 3rd Offense
            @else {{ $ticket->offense_number }}th Offense
            @endif
        </p>

        <table class="table table-sm table-borderless mb-2">
            <tr>
                <td style="font-weight: bold;">Provisions:</td>
                <td>
                    @php
                    $violationTypes = $ticket->violation_type ? explode(',', $ticket->violation_type) : [];
                    @endphp
                    @if(count($violationTypes) > 0)
                    @foreach($violationTypes as $type)
                    [ {{ trim($type) }} ]<br>
                    @endforeach
                    @else
                    [ ]
                    @endif
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Total Amount:</td>
                <td><strong>PHP{{ number_format($ticket->total_amount, 2) }}</strong></td>
            </tr>
            <tr>
                <td>Status:</td>
                <td><span class="badge badge-warning">Pending</span></td>
            </tr>
        </table>
    </div>
</div>

<div class="modal-footer">
    <button class="btn btn-success" onclick="printCitationToPT210()">
        🖨️ Print Ticket
    </button>
    <button class="btn btn-primary" id="send-sms" data-license-id="{{ $driver->license_id }}">
        <i class="fas fa-paper-plane"></i> Send SMS
    </button>
</div>