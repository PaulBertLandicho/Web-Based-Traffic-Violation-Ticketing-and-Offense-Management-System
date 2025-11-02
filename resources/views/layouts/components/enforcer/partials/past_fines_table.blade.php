@if(count($results) > 0)

<!-- SINGLE ISSUE FINE BUTTON (Top of the Table) -->
<div class="mb-2">
    <a href="{{ route('fine.create', ['license_id' => $results[0]->license_id]) }}"
        class="btn btn-success btn-sm">
        <i class="fas fa-plus-circle"></i> Issue New Fine
    </a>
</div>

<!-- Table -->
<div class="table-responsive mt-2" style="max-height: 400px; overflow-y: auto;">
    <table class="table table-bordered table-striped table-sm">
        <thead class="thead-dark">
            <tr>
                <th>License No</th>
                <th>Violations</th>
                <th>Plate No</th>
                <th>Place</th>
                <th>Status</th>
                <th>Issued Date</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th>License No</th>
                <th>Violations</th>
                <th>Plate No</th>
                <th>Place</th>
                <th>Status</th>
                <th>Issued Date</th>
            </tr>
        </tfoot>
        <tbody>
            @foreach($results as $fine)
            <tr>
                <td>{{ $fine->license_id }}</td>
                <td>{{ $fine->violation_type }}</td>
                <td>{{ $fine->vehicle_no }}</td>
                <td>{{ $fine->place }}</td>
                <td>
                    <span class="badge badge-{{ $fine->status == 'pending' ? 'warning' : 'success' }}">
                        {{ ucfirst($fine->status) }}
                    </span>
                </td>
                <td>{{ \Carbon\Carbon::parse($fine->issued_date)->format('F d, Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@else
<div class="alert alert-warning mt-3">
    No fines found for license ID <strong>{{ request('licenseid') }}</strong>.
</div>
@endif