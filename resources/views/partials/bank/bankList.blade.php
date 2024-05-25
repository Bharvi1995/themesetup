<style type="text/css">
	.form-check.form-check-inline.disabled,
	.form-check.form-check-inline.disabled label,
	.form-check.form-check-inline.disabled input{
		cursor: not-allowed;
	}
</style>
<div class="row">
@if(!empty($data) && count($data) != 0)
    @foreach($data as $key=>$value)
    	<div class="col-md-6">
    		<div class="form-check form-check-inline {{ $value->status != 0 ? 'disabled' : '' }}">
	    	<label class="form-check-label">
	            <input id="{{ $value->id }}" type="checkbox" name="bank[]" value="{{ $value->id }}" {{ $value->bank_user_id != null ? 'checked' : '' }} {{ $value->status != 0 ? 'disabled' : '' }}>
	            
	            {{ $value->bankCompanyName }}
    			
    			@if($value->status == 2)
    				- <span class="badge badge-danger badge-xs">Declined</span>
				@endif
    			@if($value->status == 1)
    				- <span class="badge badge-success badge-xs">Approved</span>
				@endif
				@if($value->status == 3)
    				- <span class="badge badge-warning badge-xs">Refereed</span>
				@endif
	        </label>
    		</div>
    	</div>
	@endforeach
@else	
    <div class="col-md-12">
    	<h5>Bank Not Found.</h5>
    </div>
@endif
</div>