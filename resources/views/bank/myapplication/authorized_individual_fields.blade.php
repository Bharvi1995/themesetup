<div class="form-group col-lg-3">
    <label for="authorized_individual_name[]">Name<span class="text-danger">*</span></label>
    <div class="input-div">
        {!! Form::text('authorized_individual_name[]', '', array('placeholder' => 'Enter here...','class' =>
        'form-control','id'=>'authorized_individual_name[]')) !!}
    </div>
    @if ($errors->has('authorized_individual_name[]'))
    <span class="text-danger help-block form-error">
        {{ $errors->first('authorized_individual_name[]') }}
    </span>
    @endif
</div>

<div class="form-group col-lg-3">
    <label for="authorized_individual_phone_number[]">Phone No.<span class="text-danger">*</span></label>
    <div class="input-div">
        {!! Form::text('authorized_individual_phone_number[]', '', array('placeholder' => 'Enter here...','class' =>
        'form-control','id'=>'authorized_individual_phone_number[]')) !!}
    </div>
    @if ($errors->has('authorized_individual_phone_number[]'))
    <span class="text-danger help-block form-error">
        {{ $errors->first('authorized_individual_phone_number[]') }}
    </span>
    @endif
</div>

<div class="form-group col-lg-3">
    <label for="authorized_individual_email[]">Email<span class="text-danger">*</span></label>
    <div class="input-div">
        {!! Form::text('authorized_individual_email[]', '', array('placeholder' => 'Enter here...','class' =>
        'form-control','id'=>'authorized_individual_email[]')) !!}
    </div>
    @if ($errors->has('authorized_individual_email[]'))
    <span class="text-danger help-block form-error">
        {{ $errors->first('authorized_individual_email[]') }}
    </span>
    @endif
</div>
      
<div class="form-group col-lg-3" style="margin-top: 38px;"> 
    <button type="button" class="btn btn-primary btn-sm btnMinus"
    onClick="fnRemoveRow({groupId})"> <i class="fa fa-minus"></i>
    </button>
</div>