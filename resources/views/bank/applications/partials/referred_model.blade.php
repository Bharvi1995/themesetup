<div class="modal fade bs-example-modal-center" id="referredModel" tabindex="-1" role="referredModel" aria-hidden="true"
  style="display: none; padding-right: 15px;">
  <form id="referredForm">
    {{ csrf_field() }}
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Reason for Referral</h4>
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Note</label>
            <textarea class="form-control" name="referred_note" id="referred_note" rows="3"
              placeholder="Write Here Your Note for referred"></textarea>
            <span style="color: red;" id="referred_note_error"></span>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" id="submitReferredForm" data-link="{{ route('application-referred') }}"
            class="btn btn-success btn-sm">Confirm Submit</button>
          <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal" id="closeReferredForm">Close</button>
        </div>
      </div>
    </div>
  </form>
</div>