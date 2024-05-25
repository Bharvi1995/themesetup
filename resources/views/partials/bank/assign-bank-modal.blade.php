<div class="modal right fade" id="assignBankModal" tabindex="-1" role="dialog" aria-labelledby="right_modal_lg">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Send To Bank</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                </button>
            </div>
            <form id="assignBankForm">
                {{ csrf_field() }}
                <input type="hidden" value="" name="application_id">
                <div class="modal-body" id="bank-list">

                </div>
                <span class="help-block text-danger">
                    <strong id="sent_to_bank_error"></strong>
                </span>
                <div class="modal-footer modal-footer-fixed">
                    <button type="button" class="btn btn-danger  closeAssignBankForm"
                        data-bs-dismiss="modal">Close</button>

                    <button type="button" class="btn btn-primary " id="submitAssignBankForm">Send</button>
                </div>
            </form>
        </div>
    </div>
</div>
