@extends($agentUserTheme)

@section('title')
    Verification
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('rp.dashboard') }}">Dashboard</a> / Verification
@endsection

@section('customStyle')
    <style>
        .imgHome{
            max-width:300px;
        }
        .rowHome{
            justify-content: center !important;
        }
    </style>
    <link href="{{ storage_asset('themeAdmin/css/selectize.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body p30">
                    <div class="row rowHome">
                        <img src="{{ storage_asset('setup/images/home.svg') }}" class="imgHome" >
                    </div>
                    <div class="row text-center">
                        <div class="col-md-12">
                            <h4 class="mb-2 mt-2 h4Home">Welcome to testpay!</h4>
                            <p class="mb-2">Please wait for your customer relationship manager to reach out to you. They will contact you shortly to assist you further.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- <div class="row">
        <div class="col-xl-12 col-xxl-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Application</h4>
                </div>
                <div class="card-body">

                    <div class="basic-form"> -->
                        <!-- <form action="{{ route('rp.my-application.store') }}" method="post" enctype="multipart/form-data"
                            id="application-form" class="form-dark">
                            @csrf
                            <div class="row">
                                <div class="form-group col-lg-4">
                                    <label for="company_name">Entity Name<span class="text-danger">*</span></label>
                                    <div class="input-div">
                                        {!! Form::text('company_name', '', [
                                            'placeholder' => 'Enter here...',
                                            'class' => 'form-control',
                                            'id' => 'company_name',
                                        ]) !!}
                                    </div>
                                    @if ($errors->has('company_name'))
                                        <span class="text-danger help-block form-error">
                                            {{ $errors->first('company_name') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="form-group col-lg-4">
                                    <label for="website_url">Website URL<span class="text-danger">*</span></label>
                                    <div class="input-div">
                                        {!! Form::text('website_url', '', [
                                            'placeholder' => 'Enter here...',
                                            'class' => 'multi-select',
                                            'id' => 'website_url',
                                            'multiple' => 'multiple',
                                        ]) !!}
                                    </div>
                                    <small>Press <kbd>Tab</kbd> after each input and <kbd>left/right arrow keys</kbd> to
                                        move the cursor between values.</small>
                                    @if ($errors->has('website_url'))
                                        <span class="text-danger help-block form-error">
                                            {{ $errors->first('website_url') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group col-lg-4">
                                    <label for="company_registered_number">Tax Id<span
                                            class="text-danger">*</span></label>
                                    <div class="input-div">
                                        {!! Form::text('company_registered_number', '', [
                                            'placeholder' => 'Enter here...',
                                            'class' => 'form-control',
                                            'id' => 'company_registered_number',
                                        ]) !!}
                                    </div>
                                    @if ($errors->has('company_registered_number'))
                                        <span class="text-danger help-block form-error">
                                            {{ $errors->first('company_registered_number') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group col-lg-4">
                                    <label for="company_registered_number_year">Date Of Birth/Incorporation<span
                                            class="text-danger">*</span></label>
                                    <div class="input-div">
                                        {!! Form::number('company_registered_number_year', '', [
                                            'placeholder' => 'Enter here...',
                                            'class' => 'form-control',
                                            'id' => 'company_registered_number_year',
                                        ]) !!}
                                    </div>
                                    @if ($errors->has('company_registered_number_year'))
                                        <span class="text-danger help-block form-error">
                                            {{ $errors->first('company_registered_number_year') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="form-group col-lg-4">
                                    <label for="company_address">Address<span class="text-danger">*</span></label>
                                    <div class="input-div">
                                        <textarea id="company_address" class="form-control" name="company_address" placeholder="Enter here..."
                                            value="{{ Input::old('company_address') }}" rows="1">{{ Input::old('company_address') }}</textarea>
                                    </div>
                                    @if ($errors->has('company_address'))
                                        <span class="text-danger help-block form-error">
                                            {{ $errors->first('company_address') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="form-group col-lg-4">
                                    <label for="company_email">Company Email<span class="text-danger">*</span></label>
                                    <div class="input-div">
                                        {!! Form::text('company_email', '', [
                                            'placeholder' => 'Enter here...',
                                            'class' => 'form-control',
                                            'id' => 'company_email',
                                        ]) !!}
                                    </div>
                                    @if ($errors->has('company_email'))
                                        <span class="text-danger help-block form-error">
                                            {{ $errors->first('company_email') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="form-group col-lg-4">
                                    <label for="avg_no_of_app">Average No. of Applications Per Month<span
                                            class="text-danger">*</span></label>
                                    <div class="input-div">
                                        {!! Form::number('avg_no_of_app', '', [
                                            'placeholder' => 'Enter here...',
                                            'class' => 'form-control',
                                            'id' => 'avg_no_of_app',
                                        ]) !!}
                                    </div>
                                    @if ($errors->has('avg_no_of_app'))
                                        <span class="text-danger help-block form-error">
                                            {{ $errors->first('avg_no_of_app') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group col-lg-4">
                                    <label for="commited_avg_volume_per_month">Average Volume Commited Per Month (In
                                        USD)<span class="text-danger">*</span></label>
                                    <div class="input-div">
                                        {!! Form::number('commited_avg_volume_per_month', '', [
                                            'placeholder' => 'Enter here...',
                                            'class' => 'form-control',
                                            'id' => 'commited_avg_volume_per_month',
                                        ]) !!}
                                    </div>
                                    @if ($errors->has('commited_avg_volume_per_month'))
                                        <span class="text-danger help-block form-error">
                                            {{ $errors->first('commited_avg_volume_per_month') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group col-lg-4">
                                    <label for="major_regious">Payment Solutions Needed<span
                                            class="text-danger">*</span></label>
                                    <div class="input-div">
                                        {!! Form::select('payment_solutions_needed[]', $integration_preference, old('payment_solutions_needed'), [
                                            'id' => 'payment_solutions_needed',
                                            'class' => 'form-control select2
                                                                                                                                                                                                                                                                                    payment_solutions_needed',
                                            'multiple' => 'multiple',
                                        ]) !!}
                                    </div>
                                    @if ($errors->has('payment_solutions_needed'))
                                        <span class="text-danger help-block form-error">
                                            {{ $errors->first('payment_solutions_needed') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group col-lg-4">
                                    <label for="major_regious">Industries Referred<span class="text-danger">*</span></label>
                                    <div class="input-div">
                                        {!! Form::select('industries_reffered[]', $industry_type, old('industries_reffered'), [
                                            'id' => 'industries_reffered',
                                            'class' => 'form-control select2
                                                                                                                                                                                                                                                                                    industries_reffered',
                                            'multiple' => 'multiple',
                                        ]) !!}
                                    </div>
                                    @if ($errors->has('industries_reffered'))
                                        <span class="text-danger help-block form-error">
                                            {{ $errors->first('industries_reffered') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group col-lg-4">
                                    <label for="major_regious">Major Regions<span class="text-danger">*</span></label>
                                    <div class="input-div">
                                        {!! Form::select(
                                            'major_regious[]',
                                            ['UK' => 'UK', 'EU' => 'EU', 'US/CANADA' => 'US/CANADA', 'ASIA' => 'ASIA', 'AFRICA' => 'AFRICA'],
                                            old('major_regious'),
                                            [
                                                'id' => 'major_regious',
                                                'class' => 'form-control select2 major_regious',
                                                'multiple' => 'multiple',
                                                'onchange' => 'getProcessingCountry(this)',
                                            ],
                                        ) !!}
                                    </div>
                                    @if ($errors->has('major_regious'))
                                        <span class="text-danger help-block form-error">
                                            {{ $errors->first('major_regious') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group col-lg-4">
                                    <label for="company_email">How are the leads generated?</label>
                                    <div class="input-div">
                                        {!! Form::text('generated_lead', '', [
                                            'placeholder' => 'Enter here...',
                                            'class' => 'form-control',
                                            'id' => 'generated_lead',
                                        ]) !!}
                                    </div>
                                    @if ($errors->has('generated_lead'))
                                        <span class="text-danger help-block form-error">
                                            {{ $errors->first('generated_lead') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-2 mt-2">
                                    <h5>Application Documents <small class="text-primary">The document size should not
                                            exceed 35MB</small>
                                    </h5>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Passport <span class="text-danger">*</span></label>
                                    <div class="custom-file">
                                        <input type="file" class="form-control" id="validationCustomFile1"
                                            name="passport[]" Required>


                                    </div>
                                    <div class="dynamicPassportFields"></div>
                                    @if ($errors->has('passport.*'))
                                        <span class="text-danger help-block form-error">
                                            {{ $errors->first('passport.*') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Utility Bill / Bank Account statement <span class="text-danger">*</span></label>
                                    <div class="custom-file">
                                        <input type="file" class="form-control" id="validationCustomFile2"
                                            name="utility_bill[]" Required>


                                    </div>
                                    <div class="dynamicUtilityBillFields"></div>
                                    @if ($errors->has('utility_bill.*'))
                                        <span class="text-danger help-block form-error">
                                            {{ $errors->first('utility_bill.*') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Company certificate of incorporation <span class="text-danger">*</span></label>
                                    <div class="custom-file">
                                        <input type="file" class="form-control" id="validationCustomFile3"
                                            name="company_incorporation_certificate" Required>

                                    </div>
                                    <div class="dynamicCertificateOfIncorporationFields"></div>
                                    @if ($errors->has('company_incorporation_certificate'))
                                        <span class="text-danger help-block form-error">
                                            {{ $errors->first('company_incorporation_certificate') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Tax ID (IF available)</label>
                                    <div class="custom-file">
                                        <input type="file" class="form-control" id="validationCustomFile4"
                                            name="tax_id">


                                    </div>
                                    <div class="dynamicTaxIdFields"></div>
                                    @if ($errors->has('tax_id'))
                                        <span class="text-danger help-block form-error">
                                            {{ $errors->first('tax_id') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <h5 class="mb-2 mt-2">Authorized Individual</h5>
                            <div id="sec_authorized_individual">
                                <div class="row">
                                    <div class="form-group col-lg-3">
                                        <label for="authorized_individual_name[]">Name<span
                                                class="text-danger">*</span></label>
                                        <div class="input-div">
                                            {!! Form::text('authorized_individual_name[]', '', [
                                                'placeholder' => 'Enter here...',
                                                'class' => 'form-control',
                                                'id' => 'authorized_individual_name[]',
                                            ]) !!}
                                        </div>
                                        @if ($errors->has('authorized_individual_name[]'))
                                            <span class="text-danger help-block form-error">
                                                {{ $errors->first('authorized_individual_name[]') }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="form-group col-lg-3">
                                        <label for="authorized_individual_phone_number[]">Phone No.<span
                                                class="text-danger">*</span></label>
                                        <div class="input-div">
                                            {!! Form::text('authorized_individual_phone_number[]', '', [
                                                'placeholder' => 'Enter here...',
                                                'class' => 'form-control',
                                                'id' => 'authorized_individual_phone_number[]',
                                            ]) !!}
                                        </div>
                                        @if ($errors->has('authorized_individual_phone_number[]'))
                                            <span class="text-danger help-block form-error">
                                                {{ $errors->first('authorized_individual_phone_number[]') }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="form-group col-lg-3">
                                        <label for="authorized_individual_email[]">Email<span
                                                class="text-danger">*</span></label>
                                        <div class="input-div">
                                            {!! Form::text('authorized_individual_email[]', '', [
                                                'placeholder' => 'Enter here...',
                                                'class' => 'form-control',
                                                'id' => 'authorized_individual_email[]',
                                            ]) !!}
                                        </div>
                                        @if ($errors->has('authorized_individual_email[]'))
                                            <span class="text-danger help-block form-error">
                                                {{ $errors->first('authorized_individual_email[]') }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="form-group col-lg-3" style="margin-top: 38px;">
                                        <button type="button" class="btn btn-success btn-sm" id="btnPlus">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <button name="action" type="submit" id="submit_button" class="btn btn-primary"
                                    value="save">Submit</button>
                                <a href="" class="btn btn-danger">Cancel</a>
                            </div>

                        </form> -->
                        <!-- <div id="row_authorized_individual" style="display: none;">
                            <div class="row">
                                <div class="form-group col-lg-3">
                                    <label for="authorized_individual_name[]">Name<span
                                            class="text-danger">*</span></label>
                                    <div class="input-div">
                                        {!! Form::text('authorized_individual_name[]', '', [
                                            'placeholder' => 'Enter
                                                                                                                                                                                                                                                                                    here...',
                                            'class' => 'form-control',
                                            'id' => 'authorized_individual_name[]',
                                        ]) !!}
                                    </div>
                                    @if ($errors->has('authorized_individual_name[]'))
                                        <span class="text-danger help-block form-error">
                                            {{ $errors->first('authorized_individual_name[]') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="form-group col-lg-3">
                                    <label for="authorized_individual_phone_number[]">Phone No.<span
                                            class="text-danger">*</span></label>
                                    <div class="input-div">
                                        {!! Form::text('authorized_individual_phone_number[]', '', [
                                            'placeholder' => 'Enter here...',
                                            'class' => 'form-control',
                                            'id' => 'authorized_individual_phone_number[]',
                                        ]) !!}
                                    </div>
                                    @if ($errors->has('authorized_individual_phone_number[]'))
                                        <span class="text-danger help-block form-error">
                                            {{ $errors->first('authorized_individual_phone_number[]') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="form-group col-lg-3">
                                    <label for="authorized_individual_email[]">Email<span
                                            class="text-danger">*</span></label>
                                    <div class="input-div">
                                        {!! Form::text('authorized_individual_email[]', '', [
                                            'placeholder' => 'Enter here...',
                                            'class' => 'form-control',
                                            'id' => 'authorized_individual_email[]',
                                        ]) !!}
                                    </div>
                                    @if ($errors->has('authorized_individual_email[]'))
                                        <span class="text-danger help-block form-error">
                                            {{ $errors->first('authorized_individual_email[]') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="form-group col-lg-3" style="margin-top: 38px;">
                                    <button type="button" class="btn btn-primary btn-sm btnMinus"> <i
                                            class="fa fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                        </div> -->
                    <!-- </div>
                </div>
            </div>
        </div>
    </div> -->
@endsection

@section('customScript')
    <script src="{{ storage_asset('themeAdmin/custom_js/jquery.validate.min.js') }}"></script>
    <script src="{{ storage_asset('themeAdmin/js/selectize.min.js') }}"></script>
    <script type="text/javascript">
        $("#company_registered_number_year").flatpickr({
            dateFormat: "d-m-Y",
        });

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
        $('#is_license_applied').on('change', function() {
            if ($(this).is(':checked')) {
                $('.license').show();
            } else {
                $('.license').hide();
            }
        });

        $('#btnPlus').on('click', function() {
            $('#sec_authorized_individual').append($('#row_authorized_individual').html());
        });

        $(document).on('click', '.btnMinus', function() {
            $(this).closest('.row').remove();
        })

        $('.select2').select2();
    </script>
@endsection
