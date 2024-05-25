{{-- ReAssign Model --}}
<div class="modal fade bs-example-modal-center" id="reassignModel" tabindex="-1" role="reassignModel" aria-hidden="true"
    style="display: none; padding-right: 15px;">
    <form id="reassignForm">
        {{ csrf_field() }}
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Reason For Reassign</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" name="reassign_reason" id="reassign_reason" rows="3" placeholder="Enter here"></textarea>
                        <span class="help-block text-danger">
                            <strong id="reassign_reason_error"></strong>
                        </span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="submitReassignForm" data-link="{{ route('application-reassign') }}"
                        class="btn btn-success btn-sm">Submit</button>
                    <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal"
                        id="closeReassignForm">Close</button>
                </div>
            </div>
        </div>
    </form>
</div>
{{-- Reject Model --}}
<div class="modal fade bs-example-modal-center" id="rejectModel" tabindex="-1" role="rejectModel" aria-hidden="true"
    style="display: none; padding-right: 15px;">
    <form id="rejectForm">
        {{ csrf_field() }}
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Reason For Reject</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Reject Reason</label>
                        <textarea class="form-control" name="reject_reason" id="reject_reason" rows="3"
                            placeholder="Write Here Your Reject Reason"></textarea>
                        <span class="help-block text-danger">
                            <strong id="reject_reason_error"></strong>
                        </span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="submitRejectForm" data-link="{{ route('application-reject') }}"
                        class="btn btn-success btn-sm">Submit</button>
                    <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal"
                        id="closeRejectForm">Close</button>
                </div>
            </div>
        </div>
    </form>
</div>
{{-- ReAssign Agreement Model --}}
<div class="modal fade bs-example-modal-center" id="reassignAgreementModel" tabindex="-1" role="reassignAgreementModel"
    aria-hidden="true" style="display: none; padding-right: 15px;">
    <form id="reassignAgreementForm">
        {{ csrf_field() }}
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Reason For Reassign</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" name="reassign_agreement_reason" id="reassign_agreement_reason" rows="3"
                            placeholder="Enter here"></textarea>
                        <span class="help-block text-danger">
                            <strong id="reassign_agreement_reason_error"></strong>
                        </span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="submitReassignAgreementForm"
                        data-link="{{ route('agreement-reassign') }}" class="btn btn-success btn-sm">Submit</button>
                    <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal"
                        id="closeReassignAgreementForm">Close</button>
                </div>
            </div>
        </div>
    </form>
</div>
{{-- Delete Document Modal --}}
<div class="modal fade" id="delete_doc_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <form id="DeleteDocumentForm">
        {{ csrf_field() }}
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Delete Record </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"><span>×</span></button>
                </div>
                <div class="modal-body">
                    Are you sure want to delete the document ?
                </div>
                <div class="modal-footer">
                    <button type="button" id="submitDeleteDocForm"
                        data-link="{{ route('application-delete-docs') }}"
                        class="btn btn-success btn-sm">Delete</button>
                    <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal"
                        id="closeDeleteDocForm">Close</button>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- * Resent agreement confirmation modal --}}
<div class="modal fade" id="resendAgreementMailModal" tabindex="-1" tabindex="-1"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Are you sure?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                </button>
            </div>
            <div class="modal-body">
                <p>Do you want to resend agreement email to the user.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="resendAgreementMailBtn">Yes Send it!</button>
            </div>
        </div>
    </div>
</div>
