<div class="modal fade bs-example-modal-center" id="declinedModel" tabindex="-1" role="declinedModel" aria-hidden="true"
  style="display: none; padding-right: 15px;">
  <form id="declinedForm">
    {{ csrf_field() }}
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Reason For Declined</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Declined Reason</label>
            <textarea class="form-control" name="declined_reason" id="declined_reason" rows="3"
              placeholder="Write Here Your Declined Reason"></textarea>
            <span style="color: red;" id="declined_reason_error"></span>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" id="submitDeclinedForm" data-link="{{ route('application-declined') }}"
            class="btn btn-success btn-sm">Confirm Submit</button>
          <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal" id="closeDeclinedForm">Close</button>
        </div>
      </div>
    </div>
  </form>
</div>