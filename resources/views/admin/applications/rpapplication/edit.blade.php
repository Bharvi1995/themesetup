@extends('layouts.admin.default')

@section('title')
    RP Application Edit
@endsection

@section('breadcrumbTitle')
     <nav aria-label="breadcrumb">
       <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('application-rp.all') }}">RP Applications</a></li>
          <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Edit</li>
       </ol>
       <h6 class="font-weight-bolder mb-0">Edit</h6>
    </nav>
@endsection

@section('customeStyle')
    <link href="{{ storage_asset('themeAdmin/css/selectize.css') }}" rel="stylesheet" type="text/css" />
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 col-xxl-12">
            <div class="card  mt-1">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">Application</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="basic-form">
                        <form action="{{ route('application-rp.update') }}" method="post" enctype="multipart/form-data"
                            id="application-form" class="form-dark">
                            @csrf
                            <input type="hidden" name="id" value="{{ $application->id }}">
                            <div class="row ">

                                <div class="form-group col-lg-4">
                                    <label for="company_name">Entity Name<span class="text-danger">*</span></label>
                                    <div class="input-div">
                                        {!! Form::text('company_name', $application->company_name, [
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
                                    <label for="website_url">Website URL<span class="text-danger">*</span> </label>
                                    <div class="input-div">
                                        {!! Form::text('website_url', $application->website_url, [
                                            'placeholder' => 'Enter here...',
                                            'class' => 'multi-select',
                                            'id' => 'website_url',
                                            'multiple' => 'multiple',
                                        ]) !!}
                                    </div>
                                    <small>Press <kbd class="badge badge-danger">Tab</kbd> after each input and <kbd
                                            class="badge badge-danger">left/right arrow keys</kbd> to
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
                                        {!! Form::text('company_registered_number', $application->company_registered_number, [
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
                                        {!! Form::text('company_registered_number_year', $application->company_registered_number_year, [
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
                                            value="{{ $application->company_address ?? Input::old('company_address') }}" rows="1">{{ $application->company_address ?? Input::old('company_address') }}</textarea>
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
                                        {!! Form::text('company_email', $application->company_email, [
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
                                        {!! Form::number('avg_no_of_app', $application->avg_no_of_app, [
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
                                        {!! Form::number('commited_avg_volume_per_month', $application->commited_avg_volume_per_month, [
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
                                    <label for="payment_solutions_needed">Payment Solutions Needed<span
                                            class="text-danger">*</span></label>
                                    <div class="input-div">
                                        {!! Form::select(
                                            'payment_solutions_needed[]',
                                            $integration_preference,
                                            isset($application->payment_solutions_needed) ? json_decode($application->payment_solutions_needed) : [],
                                            [
                                                'id' => 'payment_solutions_needed',
                                                'class' => 'form-control select2 payment_solutions_needed',
                                                'multiple' => 'multiple',
                                            ],
                                        ) !!}
                                    </div>
                                    @if ($errors->has('payment_solutions_needed'))
                                        <span class="text-danger help-block form-error">
                                            {{ $errors->first('payment_solutions_needed') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="form-group col-lg-4">
                                    <label for="industries_reffered">Industries Referred<span
                                            class="text-danger">*</span></label>
                                    <div class="input-div">
                                        {!! Form::select(
                                            'industries_reffered[]',
                                            $industry_type,
                                            isset($application->industries_reffered) ? json_decode($application->industries_reffered) : [],
                                            ['id' => 'industries_reffered', 'class' => 'form-control select2 industries_reffered', 'multiple' => 'multiple'],
                                        ) !!}
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
                                            isset($application->major_regious) ? json_decode($application->major_regious) : [],
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
                                        {!! Form::text('generated_lead', $application->generated_lead, [
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
                            <div class="row ">
                                <div class="col-md-12 mb-2 mt-2">
                                    <h5>Application Documents <small class="text-primary">The document size should not
                                            exceed 35MB</small>
                                    </h5>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Passport <span class="text-danger">*</span></label>
                                    {{-- <i class="fa fa-info tol-info" data-bs-toggle="tooltip" data-placement="top"
                                    title="In order to add multiple documents , please click on 'CTRL' and select the multiple files."></i> --}}
                                    <div class="custom-file">
                                        <input type="file" class="form-control" id="validationCustomFile1"
                                            name="passport[]">


                                    </div>
                                    <div class="dynamicPassportFields"></div>
                                    @if ($errors->has('passport.*'))
                                        <span class="text-danger help-block form-error">
                                            {{ $errors->first('passport.*') }}
                                        </span>
                                    @endif
                                    @if ($application->passport != null)
                                        <div class="row">
                                            @foreach (json_decode($application->passport) as $key => $value)
                                                <div class="col-md-4 mt-2">
                                                    <p class="pull-left mb-0">File - {{ $key + 1 }}</p>
                                                    <a href="{{ getS3Url($value) }}" target="_blank"
                                                        class="mr-4 btn btn-primary btn-sm pull-right">View</a>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Utility Bill / Bank Account statement <span class="text-danger">*</span></label>
                                    <div class="custom-file">
                                        <input type="file" class="form-control" id="validationCustomFile2"
                                            name="utility_bill[]">


                                    </div>
                                    <div class="dynamicUtilityBillFields"></div>
                                    @if ($errors->has('utility_bill.*'))
                                        <span class="text-danger help-block form-error">
                                            {{ $errors->first('utility_bill.*') }}
                                        </span>
                                    @endif
                                    @if ($application->utility_bill != null)
                                        <div class="row">
                                            @foreach (json_decode($application->utility_bill) as $key => $value)
                                                <div class="col-md-4 mt-2">
                                                    <p class="pull-left mb-0">File - {{ $key + 1 }}</p>
                                                    <a href="{{ getS3Url($value) }}" target="_blank"
                                                        class="btn mr-4 btn-primary btn-sm pull-right">View</a>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Company certificate of incorporation <span class="text-danger">*</span></label>
                                    @if ($application->company_incorporation_certificate != null)
                                        <div class="row">
                                            <div class="col-lg-10">
                                                <div class="custom-file">
                                                    <input type="file" class="form-control" id="validationCustomFile3"
                                                        name="company_incorporation_certificate">
                                                </div>
                                                <div class="dynamicCertificateOfIncorporationFields"></div>
                                                @if ($errors->has('company_incorporation_certificate'))
                                                    <span class="text-danger help-block form-error">
                                                        {{ $errors->first('company_incorporation_certificate') }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="col-lg-2">
                                                <a href="{{ getS3Url($application->company_incorporation_certificate) }}"
                                                    target="_blank" class="btn btn-primary">View</a>
                                            </div>
                                        </div>
                                </div>
                            @else
                                <div class="custom-file">
                                    <input type="file" class="form-control" id="validationCustomFile3"
                                        name="company_incorporation_certificate">
                                </div>
                                <div class="dynamicCertificateOfIncorporationFields"></div>
                                @if ($errors->has('company_incorporation_certificate'))
                                    <span class="text-danger help-block form-error">
                                        {{ $errors->first('company_incorporation_certificate') }}
                                    </span>
                                @endif
                                @endif
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
                                    @if ($application->tax_id != null)
                                        <a href="{{ getS3Url($application->tax_id) }}" target="_blank"
                                            class="btn btn-primary btn-sm">View</a>
                                    @endif
                                </div>
                            </div>
                            <h5 class="mb-2 mt-2">Authorized Individual</h5>
                            <div id="sec_authorized_individual">
                                <div class="row dynamicRow">
                                    <div class="form-group col-lg-3">
                                        <label for="authorized_individual_name[]">Name<span
                                                class="text-danger">*</span></label>
                                        <div class="input-div">
                                            {!! Form::text('authorized_individual_name[]', json_decode($application->authorised_individual)[0]->name, [
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
                                            {!! Form::text(
                                                'authorized_individual_phone_number[]',
                                                json_decode($application->authorised_individual)[0]->phone_number,
                                                ['placeholder' => 'Enter here...', 'class' => 'form-control', 'id' => 'authorized_individual_phone_number[]'],
                                            ) !!}
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
                                            {!! Form::text('authorized_individual_email[]', json_decode($application->authorised_individual)[0]->email, [
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
                                        <button type="button" class="btn btn-primary btn-sm" id="btnPlus">
                                            Plus
                                        </button>
                                    </div>
                                </div>
                                <?php
                                $values = json_decode($application->authorised_individual);
                                unset($values[0]);
                                // dd($values[1]->name);
                                ?>
                                @foreach ($values as $key => $value)
                                    <div class="row dynamicRow">
                                        <div class="form-group col-lg-3">
                                            <label for="authorized_individual_name[]">Name<span
                                                    class="text-danger">*</span></label>
                                            <div class="input-div">
                                                {!! Form::text('authorized_individual_name[]', $value->name, [
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
                                                {!! Form::text('authorized_individual_phone_number[]', $value->phone_number, [
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
                                                {!! Form::text('authorized_individual_email[]', $value->email, [
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
                                            <button type="button" class="btn btn-danger btn-sm btnMinus"> <i
                                                    class="fa fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="col-md-12">
                                <button name="action" type="submit" id="submit_button"
                                    class="btn btn-primary btn-raised" value="save">Submit</button>
                                <a href="{{ route('application-rp.detail', $application->id) }}"
                                    class="btn btn-primary">Cancel</a>
                            </div>

                        </form>
                        <div id="row_authorized_individual" style="display: none;">
                            <div class="row dynamicRow">
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
                                    <button type="button" class="btn btn-danger btn-sm btnMinus"> <i
                                            class="fa fa-minus"></i>
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

        $("#company_registered_number_year").flatpickr({
            dateFormat: "d-m-Y",
        });

        $('#btnPlus').on('click', function() {
            $('#sec_authorized_individual').append($('#row_authorized_individual').html());
        });

        $(document).on('click', '.btnMinus', function() {
            $(this).parents('.dynamicRow').remove();
        })

        $('.select2').select2();
    </script>
@endsection
