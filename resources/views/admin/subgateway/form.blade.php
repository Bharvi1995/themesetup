<div class="form-group row">
    <label class="col-sm-2 form-control-label">Name<span class="text-danger">*</span></label>
    <div class="col-sm-4">
        {!! Form::text('name', Input::get('name'), array('placeholder' => 'Enter Name','class' => 'form-control')) !!}
        @if ($errors->has('name'))
            <span class="text-danger help-block form-error">
                <strong>{{ $errors->first('name') }}</strong>
            </span>
        @endif
    </div>
</div>
@foreach (json_decode($gateway->credential_titles) as $key=>$value)
    <div class="form-group row">
        <label class="col-sm-2 form-control-label">{{ $value }}<span class="text-danger">*</span></label>
        <div class="col-sm-4">
            {!! Form::text($key, Input::get($key), array('placeholder' => $value,'class' => 'form-control')) !!}
            @if ($errors->has($key))
                <span class="text-danger help-block form-error">
                    <strong>{{ $errors->first($key) }}</strong>
                </span>
            @endif
        </div>
    </div>
@endforeach
<hr>
<div class="row">
    <div class="col-md-8 col-md-offset-4 mt-1">
        <button type="submit" class="btn btn-primary">Save
        </button>
        <button type="reset" class="btn btn-danger">Cancel
        </button>
    </div>
</div>