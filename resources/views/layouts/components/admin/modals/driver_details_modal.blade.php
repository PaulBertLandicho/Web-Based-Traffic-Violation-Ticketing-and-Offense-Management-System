<!-- View Modal -->
<!-- Modernized Driver Details Modal -->
<div class="modal fade" id="dataModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0 rounded-4">
            <!-- Header -->
            <div class="modal-header bg-gradient-info text-white rounded-top-4 py-3">
                <h4 class="modal-title fw-bold mb-0">
                    <i class="fas fa-id-card me-2"></i> Driver Details Record
                </h4>
                <button type="button" class="btn-close btn-close-white" data-dismiss="modal"></button>
            </div>

            <!-- Body -->
            <div class="modal-body px-4 py-3" id="driver_detail">
                <!-- Data will be injected by jQuery -->
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
                <div class="modal-header bg-success text-white">
                    <h4 class="modal-title"><i class="fas fa-edit"></i> Edit Driver</h4>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="license_id" id="edit_license_id">
                    <div class="form-group"><label for="edit_driver_name">Driver Name</label><input type="text" name="driver_name" id="edit_driver_name" class="form-control"></div>
                    <div class="form-group"><label for="edit_home_address">Address</label><input type="text" name="home_address" id="edit_home_address" class="form-control"></div>
                    <div class="form-group"><label for="edit_license_issue_date">License Issue Date</label><input type="date" name="license_issue_date" id="edit_license_issue_date" class="form-control"></div>
                    <div class="form-group"><label for="edit_license_expire_date">License Expire Date</label><input type="date" name="license_expire_date" id="edit_license_expire_date" class="form-control"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Update</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>