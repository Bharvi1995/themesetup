@extends('layouts.bank.default')

@section('title')
My Application Create
@endsection

@section('breadcrumbTitle')
<a href="{{ route('dashboardPage') }}">Dashboard</a> / <a href="{{ route('my-application') }}"> My Application</a> / Create
@endsection

@section('customeStyle')
<link href="{{ storage_asset('themeAdmin/css/selectize.css') }}" rel="stylesheet" type="text/css" />
<style type="text/css">
    .selectize-input,
    .selectize-control.single .selectize-input.input-active {
        /*background: #2B2B2B !important;*/
        color: #b7aeaf !important;
    }

    small {
        color: #b7aeaf !important;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12 col-xxl-12">
        <div class="card">
            <div class="card-header">
                <div class="header-title">
                    <h4 class="card-title">Application</h4>
                </div>
            </div>
            <div class="card-body">
                <div class="basic-form">
                    <form action="{{ route('bank.my-application.store') }}" method="post" enctype="multipart/form-data"
                        id="application-form" class="form form-dark">
                        @csrf
                        <div class="form-row row">
                            <div class="form-group col-md-4">
                                <label for="company_name">Company Name<span class="text-danger">*</span></label>
                                <div class="input-div">
                                    {!! Form::text('company_name','', array('placeholder' => 'Enter here...','class' =>
                                    'form-control','id'=>'company_name')) !!}
                                </div>
                                @if ($errors->has('company_name'))
                                <span class="text-danger help-block form-error">
                                    {{ $errors->first('company_name') }}
                                </span>
                                @endif
                            </div>

                            <div class="form-group col-md-4">
                                <label for="website_url">Website URL<span class="text-danger">*</span>
                                    <small>https://example.com</small></label>
                                <div class="input-div">
                                    {!! Form::text('website_url', '', array('placeholder' => 'Enter here...','class' =>
                                    'multi-select','id'=>'website_url',"multiple" => "multiple")) !!}
                                </div>
                                <small>Press <kbd>Tab</kbd> after each input and <kbd>left/right arrow keys</kbd> to
                                    move the cursor between values.</small>
                                @if ($errors->has('website_url'))
                                <span class="text-danger help-block form-error">
                                    {{ $errors->first('website_url') }}
                                </span>
                                @endif
                            </div>

                            <div class="form-group col-md-4">
                                <label for="company_registered_number_year">Company Register Number / Year<span
                                        class="text-danger">*</span></label>
                                <div class="input-div">
                                    {!! Form::number('company_registered_number_year', '', array('placeholder' =>
                                    'Enter here...','class' =>
                                    'form-control','id'=>'company_registered_number_year')) !!}
                                </div>
                                @if ($errors->has('company_registered_number_year'))
                                <span class="text-danger help-block form-error">
                                    {{ $errors->first('company_registered_number_year') }}
                                </span>
                                @endif
                            </div>

                            <div class="form-group col-md-4">
                                <label for="company_address">Company Address<span class="text-danger">*</span></label>
                                <div class="input-div">
                                    <textarea id="company_address" class="form-control" name="company_address"
                                        placeholder="Enter here..."
                                        value="{{ Input::old('company_address') }}">{{ Input::old('company_address') }}</textarea>
                                </div>
                                @if ($errors->has('company_address'))
                                <span class="text-danger help-block form-error">
                                    {{ $errors->first('company_address') }}
                                </span>
                                @endif
                            </div>

                            <div class="form-group col-md-4">
                                <label for="settlement_method_for_crypto">Settlement Method for Crypto<span
                                        class="text-danger">*</span></label>
                                <div class="input-div">
                                    <select id="settlement_method_for_crypto" class="select2"
                                        name="settlement_method_for_crypto">
                                        <option value="">--Select--</option>
                                        <option value="USDT">USDT</option>
                                        <option value="BTC">BTC</option>
                                    </select>
                                </div>
                                @if ($errors->has('settlement_method_for_crypto'))
                                <span class="text-danger help-block form-error">
                                    {{ $errors->first('settlement_method_for_crypto') }}
                                </span>
                                @endif
                            </div>

                            <div class="form-group col-md-4">
                                <label for="settlement_method_for_fiat">Settlement Method for Fiat<span
                                        class="text-danger">*</span></label>
                                <div class="input-div">
                                    <select id="settlement_method_for_fiat" class="select2"
                                        name="settlement_method_for_fiat">
                                        <option value="">--Select--</option>
                                        <option value="USD">USD</option>
                                        <option value="GBP">GBP</option>
                                        <option value="EURO">EURO</option>
                                    </select>
                                </div>
                                @if ($errors->has('settlement_method_for_fiat'))
                                <span class="text-danger help-block form-error">
                                    {{ $errors->first('settlement_method_for_fiat') }}
                                </span>
                                @endif
                            </div>

                            <div class="form-group col-md-4">
                                <label for="mcc_codes">MCC Codes<span class="text-danger">*</span></label>
                                <div class="input-div">
                                    {!! Form::text('mcc_codes', '', array('placeholder' => 'Enter here...','class' =>
                                    'multi-select','id'=>'mcc_codes',"multiple" => "multiple")) !!}
                                </div>
                                <small>Press <kbd>Tab</kbd> after each input and <kbd>left/right arrow keys</kbd> to
                                    move the cursor between values.</small>
                                @if ($errors->has('mcc_codes'))
                                <span class="text-danger help-block form-error">
                                    {{ $errors->first('mcc_codes') }}
                                </span>
                                @endif
                            </div>

                            <div class="form-group col-md-4">
                                <label for="descriptors">Descriptors<span class="text-danger">*</span></label>
                                <div class="input-div">
                                    {!! Form::text('descriptors', '', array('placeholder' => 'Enter here...','class' =>
                                    'multi-select','id'=>'descriptors')) !!}
                                </div>
                                <small>Press <kbd>Tab</kbd> after each input and <kbd>left/right arrow keys</kbd> to
                                    move the cursor between values.</small>
                                @if ($errors->has('descriptors'))
                                <span class="text-danger help-block form-error">
                                    {{ $errors->first('descriptors') }}
                                </span>
                                @endif
                            </div>

                            <div class="form-group col-md-4">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="is_license_applied"
                                        name="is_license_applied" value="1">
                                    <label class="custom-control-label" for="is_license_applied">Is License
                                        Applied</label>
                                </div>
                                @if ($errors->has('is_license_applied'))
                                <span class="help-block text-danger">
                                    {{ $errors->first('is_license_applied') }}
                                </span>
                                @endif
                                <div style="display: none;" class="license">
                                    <label>Upload your licence.<span class="text-danger">*</span></label>

                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input form-control" name="license_image">      
                                    </div>

                                    @if ($errors->has('license_image'))
                                    <span class="text-danger help-block form-error">
                                        {{ $errors->first('license_image') }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <h5 class="mb-3 mt-3">Authorized Individual</h5>
                        <div id="sec_authorized_individual">
                            <div class="row">
                                <div class="form-group col-md-3">
                                    <label for="authorized_individual_name[]">Name<span
                                            class="text-danger">*</span></label>
                                    <div class="input-div">
                                        {!! Form::text('authorized_individual_name[]', '', array('placeholder' =>
                                        'Enter here...','class' =>
                                        'form-control','id'=>'authorized_individual_name[]')) !!}
                                    </div>
                                    @if ($errors->has('authorized_individual_name[]'))
                                    <span class="text-danger help-block form-error">
                                        {{ $errors->first('authorized_individual_name[]') }}
                                    </span>
                                    @endif
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="authorized_individual_phone_number[]">Phone No.<span class="text-danger">*</span></label>
                                    <div class="input-div">
                                        {!! Form::text('authorized_individual_phone_number[]', '', array('placeholder'
                                        => 'Enter here...','class' =>
                                        'form-control','id'=>'authorized_individual_phone_number[]')) !!}
                                    </div>
                                    @if ($errors->has('authorized_individual_phone_number[]'))
                                    <span class="text-danger help-block form-error">
                                        {{ $errors->first('authorized_individual_phone_number[]') }}
                                    </span>
                                    @endif
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="authorized_individual_email[]">Email<span
                                            class="text-danger">*</span></label>
                                    <div class="input-div">
                                        {!! Form::text('authorized_individual_email[]', '', array('placeholder' =>
                                        'Enter here...','class' =>
                                        'form-control','id'=>'authorized_individual_email[]')) !!}
                                    </div>
                                    @if ($errors->has('authorized_individual_email[]'))
                                    <span class="text-danger help-block form-error">
                                        {{ $errors->first('authorized_individual_email[]') }}
                                    </span>
                                    @endif
                                </div>

                                <div class="form-group col-md-3" style="margin-top: 38px;">
                                    <button type="button" class="btn btn-danger btn-sm" id="btnPlus"><i class="fa fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 mt-2 text-right">
                            <button name="action" type="submit" id="submit_button" class="btn btn-primary btn-raised" value="save">Submit</button>
                            <a href="" class="btn btn-danger">Cancel</a>
                        </div>

                    </form>
                    <div id="row_authorized_individual" style="display: none;">
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label for="authorized_individual_name[]">Name<span class="text-danger">*</span></label>
                                <div class="input-div">
                                    {!! Form::text('authorized_individual_name[]', '', array('placeholder' =>
                                    'Enter here...','class' =>
                                    'form-control','id'=>'authorized_individual_name[]')) !!}
                                </div>
                                @if ($errors->has('authorized_individual_name[]'))
                                <span class="text-danger help-block form-error">
                                    {{ $errors->first('authorized_individual_name[]') }}
                                </span>
                                @endif
                            </div>

                            <div class="form-group col-md-3">
                                <label for="authorized_individual_phone_number[]">Phone No.<span
                                        class="text-danger">*</span></label>
                                <div class="input-div">
                                    {!! Form::text('authorized_individual_phone_number[]', '', array('placeholder' =>
                                    'Enter here...','class' =>
                                    'form-control','id'=>'authorized_individual_phone_number[]')) !!}
                                </div>
                                @if ($errors->has('authorized_individual_phone_number[]'))
                                <span class="text-danger help-block form-error">
                                    {{ $errors->first('authorized_individual_phone_number[]') }}
                                </span>
                                @endif
                            </div>

                            <div class="form-group col-md-3">
                                <label for="authorized_individual_email[]">Email<span
                                        class="text-danger">*</span></label>
                                <div class="input-div">
                                    {!! Form::text('authorized_individual_email[]', '', array('placeholder' =>
                                    'Enter here...','class' =>
                                    'form-control','id'=>'authorized_individual_email[]')) !!}
                                </div>
                                @if ($errors->has('authorized_individual_email[]'))
                                <span class="text-danger help-block form-error">
                                    {{ $errors->first('authorized_individual_email[]') }}
                                </span>
                                @endif
                            </div>

                            <div class="form-group col-md-3" style="margin-top: 38px;">
                                <button type="button" class="btn btn-primary btn-sm btnMinus"> <i class="fa fa-minus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('customScript')
<script src="{{ storage_asset('themeAdmin/custom_js/jquery.validate.min.js') }}"></script>
<script src="{{ storage_asset('themeAdmin/js/selectize.min.js') }}"></script>
<script type="text/javascript">
    $('.multi-select').selectize({
        delimiter: ',',
        persist: false,
        create: function(input) {
            return {
                value: input,
                text: input
            }
        }
    });
    $('#is_license_applied').on('change', function(){
        if($(this).is(':checked')){
            $('.license').show();
        }else{
            $('.license').hide();
        }
    });

    $('#btnPlus').on('click', function () {
        $('#sec_authorized_individual').append($('#row_authorized_individual').html());
    });

    $(document).on('click','.btnMinus', function () {
        $(this).closest('.row').remove();
    })

    $('.select2').select2();

</script>
@endsection