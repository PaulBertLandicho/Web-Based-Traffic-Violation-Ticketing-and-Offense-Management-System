<!-- Edit Modal -->
<div class="modal fade" id="edit_data_Modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form id="edit_form">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h4 class="modal-title text-white"><i class="fas fa-pen"></i> Edit Traffic Violation Details</h4>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="violation_id" id="edit_violation_id">
                    <div class="form-group"><label for="violation_type">Violation</label><input type="text" class="form-control" id="violation_type" name="violation_type" placeholder="Provision">

                    </div>
                    <div class="form-group"><label for="violation_amount">Violation Amount</label><input type="number" class="form-control" id="violation_amount" name="violation_amount" placeholder="Fine Amount">

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