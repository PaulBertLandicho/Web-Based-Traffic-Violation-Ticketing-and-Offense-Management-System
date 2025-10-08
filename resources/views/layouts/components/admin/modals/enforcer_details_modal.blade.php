<!-- Modernized Traffic Enforcer Details Modal -->
<div class="modal fade" id="dataModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0 rounded-4">
            <!-- Header -->
            <div class="modal-header bg-gradient-info text-white rounded-top-4 py-3">
                <h4 class="modal-title fw-bold mb-0">
                    <i class="fas fa-user-shield me-2"></i> Traffic Enforcer Details
                </h4>
                <button type="button" class="btn-close btn-close-white" data-dismiss="modal"></button>
            </div>

            <!-- Body -->
            <div class="modal-body px-4 py-3" id="enforcer_detail">
                <!-- Enforcer Info -->
                <div class="card border-0 shadow-sm mb-4 rounded-4">
                    <div class="card-body">
                        <h5 class="fw-semibold text-info mb-3">
                            <i class="fas fa-id-card-alt me-2"></i> Personal Information
                        </h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <p class="mb-1 text-muted fw-bold">Name</p>
                                <p class="fs-6" id="detail_name"></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1 text-muted fw-bold">Email</p>
                                <p class="fs-6" id="detail_email"></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1 text-muted fw-bold">Assigned Area</p>
                                <p class="fs-6" id="detail_area"></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1 text-muted fw-bold">Contact No#</p>
                                <p class="fs-6" id="detail_contact"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Violations Complaint Filed -->
                <div class="card border-0 shadow-sm mb-4 rounded-4">
                    <div class="card-body">
                        <h5 class="fw-semibold text-danger mb-3">
                            <i class="fas fa-exclamation-triangle me-2"></i> Violations Complaint Filed
                        </h5>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="enforcerViolationsTable">
                                <thead class="table-light">
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
                        </div>
                    </div>
                </div>

                <!-- Drivers Issued Violations -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <h5 class="fw-semibold text-primary mb-3">
                            <i class="fas fa-car-crash me-2"></i> Drivers Issued Violations
                        </h5>

                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                            <div id="customSearch" class="flex-grow-1 me-3"></div>
                            <select id="violationFilter" class="form-select w-auto">
                                <option value="">Filter by Violation</option>
                            </select>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="violationsTable">
                                <thead class="table-light">
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
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer border-0 py-3">
                <button class="btn btn-outline-secondary rounded-pill px-4" data-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Close
                </button>
            </div>
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


<style>
    /* Modernized Modal Styling */
    .modal-content {
        border-radius: 1rem;
        overflow: hidden;
    }

    .bg-gradient-info {
        background: linear-gradient(135deg, #0dcaf0, #0275d8);
    }

    /* Card Styles */
    .card {
        border-radius: 1rem;
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    /* Tables */
    .table thead th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        color: #6c757d;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .modal-dialog {
            margin: 1rem;
        }

        .card-body {
            padding: 1rem;
        }
    }
</style>