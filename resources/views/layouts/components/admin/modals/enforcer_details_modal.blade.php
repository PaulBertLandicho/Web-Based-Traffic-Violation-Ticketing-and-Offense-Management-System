<!-- View Driver Details Modal -->
<div class="modal fade" id="dataModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h4 class="modal-title"><i class="fas fa-user"></i> Traffic Enforcer Details Record</h4>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body" id="enforcer_detail">
                <p><strong>Name:</strong> <span id="detail_name"></span></p>
                <p><strong>Email:</strong> <span id="detail_email"></span></p>
                <p><strong>Assigned Area:</strong> <span id="detail_area"></span></p>
                <p><strong>Contact No#:</strong> <span id="detail_contact"></span></p>
                <hr>
                <hr>
                <h5><strong>Violations Complaint Filed</strong></h5>
                <table class="table-responsive table-bordered" id="enforcerViolationsTable">
                    <thead>
                        <tr>
                            <th>Violation Type</th>
                            <th>Details</th>
                            <th>Penalty</th>
                            <th>Date Issued</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <hr>
                <hr>
                <h5><strong>Drivers Issued Violations:</strong></h5>

                <div class="d-flex justify-content-between align-items-center">
                    <!-- Search box (DataTables will put input here) -->
                    <div id="customSearch"></div>

                    <!-- Filter dropdown -->
                    <div>
                        <select id="violationFilter" class="form-control" style="max-width:250px;">
                            <option value="">Filter by Violation</option>
                        </select>
                    </div>
                </div>

                <table class="table-responsive table-bordered" id="violationsTable">
                    <thead>
                        <tr>
                            <th>License ID</th>
                            <th>Driver Name</th>
                            <th>Violation</th>
                            <th>Total Amount</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer"><button class="btn btn-secondary" data-dismiss="modal">Close</button></div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="edit_data_Modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form id="edit_form">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h4 class="modal-title text-white"><i class="fas fa-pen"></i> Edit Traffic Enforcer Details</h4>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="enforcer_id" id="edit_enforcer_id">
                    <div class="form-group">
                        <label for="edit_enforcer_email">Email</label>
                        <input type="email" name="enforcer_email" id="edit_enforcer_email" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="edit_enforcer_name">Driver Name</label>
                        <input type="text" name="enforcer_name" id="edit_enforcer_name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="edit_assigned_area">Class of Vehicle</label>

                        <select name="assigned_area" id="edit_assigned_area" class="form-control" required>
                            <option value="">Select Assigned Area</option>
                            @foreach ([
                            'Abuno', 'Acmac', 'Bagong Silang', 'Bonbonon', 'Bunawan', 'Buru-un', 'Dalipuga',
                            'Del Carmen', 'Digkilaan', 'Ditucalan', 'Dulag', 'Hinaplanon', 'Hindang',
                            'Kabacsanan', 'Kalilangan', 'Kiwalan', 'Lanipao', 'Luinab', 'Mahayahay',
                            'Mainit', 'Mandulog', 'Maria Cristina', 'Pala-o', 'Panoroganan', 'Poblacion',
                            'Puga-an', 'Rogongon', 'San Miguel', 'San Roque', 'Santa Elena',
                            'Santa Filomena', 'Santiago', 'Santo Rosario', 'Saray-Tibanga', 'Suarez',
                            'Tambacan', 'Tibanga', 'Tipanoy', 'Tominobo Proper',
                            'Tominobo Upper', 'Tubod', 'Ubaldo Laya', 'Upper Hinaplanon', 'Villa Verde'
                            ] as $assignedarea)
                            <option value="{{ $assignedarea }}" {{ old('assignedarea') === $assignedarea ? 'selected' : '' }}>
                                {{ $assignedarea }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Update</button>
                    <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>