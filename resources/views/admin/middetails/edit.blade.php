@extends('layouts.admin.default')
@section('title')
    Edit MID
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / <a href="{{ route('mid-feature-management.index') }}">MID
        List</a> /
    Edit
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-header">
                    <div class="header-title">
                        <h4 class="card-title">Edit MID</h4>
                    </div>
                    <a href="{{ route('mid-feature-management.index') }}" class="btn btn-primary btn-sm"><i
                            class="fa fa-arrow-left"></i></a>
                </div>
                <div class="card-body">
                    {{ Form::model($data, ['route' => ['admin.middetails.update', $data->id], 'method' => 'patch', 'id' => 'mid-form', 'class' => 'form-dark']) }}
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label for="name">Bank Name</label>
                            <input type="text" class="form-control" placeholder="Enter here..." name="bank_name"
                                value="{{ $data->bank_name }}">
                            @if ($errors->has('bank_name'))
                                <span class="help-block">
                                    <span class="text-danger">{{ $errors->first('bank_name') }}</span>
                                </span>
                            @endif
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="">Have Gateway <span class="text-danger">*</span></label>
                            <div class="basic-form">
                                <div class="form-group mb-0">
                                    <div class="form-check">
                                        <label class="form-check-label" for="rdo-3">No</label>
                                        <input type="radio" id="rdo-3" name="is_gateway_mid"
                                            class="checkradio md-radiobtn form-check-input" value="0"
                                            {{ $data->is_gateway_mid == '0' ? 'checked' : '' }}>
                                    </div>
                                    <div class="form-check">
                                        <label class="form-check-label" for="rdo-4">Yes</label>
                                        <input type="radio" id="rdo-4" name="is_gateway_mid"
                                            class="checkradio md-radiobtn form-check-input" value="1"
                                            {{ $data->is_gateway_mid == '1' ? 'checked' : '' }}>
                                    </div>
                                </div>
                                @if ($errors->has('is_gateway_mid'))
                                    <span class="help-block">
                                        <span class="text-danger">{{ $errors->first('is_gateway_mid') }}</span>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group col-lg-6">
                            <label class="">Select Gateway</label>
                            {!! Form::select(
                                'main_gateway_mid_id',
                                ['' => '-- Select Gateway --'] + $gateways,
                                [$data->main_gateway_mid_id],
                                ['class' => ' main_gateway_mid_id form-control', 'id' => 'SelectGatewayMID'],
                            ) !!}
                            @if ($errors->has('main_gateway_mid_id'))
                                <span class="help-block">
                                    <span class="text-danger">{{ $errors->first('main_gateway_mid_id') }}</span>
                                </span>
                            @endif
                        </div>
                        <div class="form-group col-lg-6">
                            <label>Assign Gateway</label>
                            {!! Form::select(
                                'assign_gateway_mid',
                                ['' => '-- Assign Gateway --'] + $subgateways,
                                [$data->assign_gateway_mid],
                                [
                                    'class' => 'select2 assign_gateway_mid form-control',
                                    'disabled' => true,
                                    'id' => 'AssignGatewayMID',
                                    $data->is_gateway_mid == 0 ? 'disabled' : '',
                                ],
                            ) !!}
                            @if ($errors->has('assign_gateway_mid'))
                                <span class="help-block">
                                    <span class="text-danger">{{ $errors->first('assign_gateway_mid') }}</span>
                                </span>
                            @endif
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="text">Select Converted Currency</label>
                            <select name="converted_currency" data-size="7" data-live-search="true"
                                class="select2 btn-primary fill_selectbtn_in own_selectbox" data-title="Location"
                                data-width="100%">
                                <option selected disabled> -- Select Converted Currency -- </option>
                                <option value="USD" {{ $data->converted_currency == 'USD' ? 'selected' : '' }}>USD
                                </option>
                                <option value="HKD" {{ $data->converted_currency == 'HKD' ? 'selected' : '' }}>HKD
                                </option>
                                <option value="GBP" {{ $data->converted_currency == 'GBP' ? 'selected' : '' }}>GBP
                                </option>
                                <option value="CNY" {{ $data->converted_currency == 'CNY' ? 'selected' : '' }}>CNY
                                </option>
                                <option value="JPY" {{ $data->converted_currency == 'JPY' ? 'selected' : '' }}>JPY
                                </option>
                                <option value="EUR" {{ $data->converted_currency == 'EUR' ? 'selected' : '' }}>EUR
                                </option>
                                <option value="AUD" {{ $data->converted_currency == 'AUD' ? 'selected' : '' }}>AUD
                                </option>
                                <option value="CAD" {{ $data->converted_currency == 'CAD' ? 'selected' : '' }}>CAD
                                </option>
                                <option value="SGD" {{ $data->converted_currency == 'SGD' ? 'selected' : '' }}>SGD
                                </option>
                                <option value="NZD" {{ $data->converted_currency == 'NZD' ? 'selected' : '' }}>NZD
                                </option>
                                <option value="TWD" {{ $data->converted_currency == 'TWD' ? 'selected' : '' }}>TWD
                                </option>
                                <option value="KRW" {{ $data->converted_currency == 'KRW' ? 'selected' : '' }}>KRW
                                </option>
                                <option value="DKK" {{ $data->converted_currency == 'DKK' ? 'selected' : '' }}>DKK
                                </option>
                                <option value="TRL" {{ $data->converted_currency == 'TRL' ? 'selected' : '' }}>TRL
                                </option>
                                <option value="MYR" {{ $data->converted_currency == 'MYR' ? 'selected' : '' }}>MYR
                                </option>
                                <option value="NGN" {{ $data->converted_currency == 'NGN' ? 'selected' : '' }}>NGN
                                </option>
                                <option value="THB" {{ $data->converted_currency == 'THB' ? 'selected' : '' }}>THB
                                </option>
                                <option value="INR" {{ $data->converted_currency == 'INR' ? 'selected' : '' }}>INR
                                </option>
                                <option value="PHP" {{ $data->converted_currency == 'PHP' ? 'selected' : '' }}>PHP
                                </option>
                                <option value="CHF" {{ $data->converted_currency == 'CHF' ? 'selected' : '' }}>CHF
                                </option>
                                <option value="SEK" {{ $data->converted_currency == 'SEK' ? 'selected' : '' }}>SEK
                                </option>
                                <option value="ILS" {{ $data->converted_currency == 'ILS' ? 'selected' : '' }}>ILS
                                </option>
                                <option value="ZAR" {{ $data->converted_currency == 'ZAR' ? 'selected' : '' }}>ZAR
                                </option>
                                <option value="RUB" {{ $data->converted_currency == 'RUB' ? 'selected' : '' }}>RUB
                                </option>
                                <option value="NOK" {{ $data->converted_currency == 'NOK' ? 'selected' : '' }}>NOK
                                </option>
                                <option value="AED" {{ $data->converted_currency == 'AED' ? 'selected' : '' }}>AED
                                </option>
                                <option value="GHS" {{ $data->converted_currency == 'GHS' ? 'selected' : '' }}>GHS
                                </option>
                                <option value="BRL" {{ $data->converted_currency == 'BRL' ? 'selected' : '' }}>BRL
                                </option>
                                <option value="CLP" {{ $data->converted_currency == 'CLP' ? 'selected' : '' }}>CLP
                                </option>
                                <option value="UGX" {{ $data->converted_currency == 'UGX' ? 'selected' : '' }}>UGX
                                </option>
                                <option value="XOF" {{ $data->converted_currency == 'XOF' ? 'selected' : '' }}>XOF
                                </option>
                                <option value="VND" {{ $data->converted_currency == 'VND' ? 'selected' : '' }}>VND
                                </option>
                                <option value="IDR" {{ $data->converted_currency == 'IDR' ? 'selected' : '' }}>IDR
                                </option>
                                <option value="PEN" {{ $data->converted_currency == 'PEN' ? 'selected' : '' }}>PEN
                                </option>
                                <option value="MXN" {{ $data->converted_currency == 'MXN' ? 'selected' : '' }}>MXN
                                </option>
                                <option value="AZN" {{ $data->converted_currency == 'AZN' ? 'selected' : '' }}>AZN
                                </option>
                            </select>
                            @if ($errors->has('converted_currency'))
                                <span class="help-block">
                                    <span class="text-danger">{{ $errors->first('converted_currency') }}</span>
                                </span>
                            @endif
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="">Provide Refund</label>
                            <div class="basic-form">
                                <div class="form-group mb-0">
                                    <div class="form-check">
                                        <label class="form-check-label" for="rdo-5">No</label>
                                        <input type="radio" id="rdo-5" name="is_provide_refund"
                                            class="checkradio form-check-input" value="0"
                                            {{ $data->is_provide_refund == '0' ? 'checked' : '' }}>
                                    </div>
                                    <div class="form-check">
                                        <label class="form-check-label" for="rdo-6">Yes</label>
                                        <input type="radio" id="rdo-6" name="is_provide_refund"
                                            class="checkradio form-check-input" value="1"
                                            {{ $data->is_provide_refund == '1' ? 'checked' : '' }}>
                                    </div>
                                </div>
                            </div>
                            @if ($errors->has('is_provide_refund'))
                                <span class="help-block">
                                    <span class="text-danger">{{ $errors->first('is_provide_refund') }}</span>
                                </span>
                            @endif
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="name">Per Day Email Limit</label>
                            <input type="number" class="form-control" placeholder="Enter here..." name="per_day_email"
                                value="{{ $data->per_day_email }}">
                            @if ($errors->has('per_day_email'))
                                <span class="help-block">
                                    <span class="text-danger">{{ $errors->first('per_day_email') }}</span>
                                </span>
                            @endif
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="name">Per Day Card Limit</label>
                            <input type="number" class="form-control" placeholder="Enter here..." name="per_day_card"
                                value="{{ $data->per_day_card }}">
                            @if ($errors->has('per_day_card'))
                                <span class="help-block">
                                    <span class="text-danger">{{ $errors->first('per_day_card') }}</span>
                                </span>
                            @endif
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="name">Per Week Email Limit</label>
                            <input type="number" class="form-control" placeholder="Enter here..." name="per_week_email"
                                value="{{ $data->per_week_email }}">
                            @if ($errors->has('per_week_email'))
                                <span class="help-block">
                                    <span class="text-danger">{{ $errors->first('per_week_email') }}</span>
                                </span>
                            @endif
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="name">Per Week Card Limit</label>
                            <input type="number" class="form-control" placeholder="Enter here..." name="per_week_card"
                                value="{{ $data->per_week_card }}">
                            @if ($errors->has('per_week_card'))
                                <span class="help-block">
                                    <span class="text-danger">{{ $errors->first('per_week_card') }}</span>
                                </span>
                            @endif
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="name">Per Month Email Limit</label>
                            <input type="number" class="form-control" placeholder="Enter here..."
                                name="per_month_email" value="{{ $data->per_month_email }}">
                            @if ($errors->has('per_month_email'))
                                <span class="help-block">
                                    <span class="text-danger">{{ $errors->first('per_month_email') }}</span>
                                </span>
                            @endif
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="name">Per Month Card Limit</label>
                            <input type="number" class="form-control" placeholder="Enter here..." name="per_month_card"
                                value="{{ $data->per_month_card }}">
                            @if ($errors->has('per_month_card'))
                                <span class="help-block">
                                    <span class="text-danger">{{ $errors->first('per_month_card') }}</span>
                                </span>
                            @endif
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="name">Minimum Transaction Limit</label>
                            <input type="number" class="form-control" step="any" placeholder="Enter here..."
                                name="min_transaction_limit" value="{{ $data->min_transaction_limit }}">
                            @if ($errors->has('min_transaction_limit'))
                                <span class="help-block">
                                    <span class="text-danger">{{ $errors->first('min_transaction_limit') }}</span>
                                </span>
                            @endif
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="name">Per Transaction Limit</label>
                            <input type="number" class="form-control" placeholder="Enter here..."
                                name="per_transaction_limit" value="{{ $data->per_transaction_limit }}">
                            @if ($errors->has('per_transaction_limit'))
                                <span class="help-block">
                                    <span class="text-danger">{{ $errors->first('per_transaction_limit') }}</span>
                                </span>
                            @endif
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="name">Per Day Limit</label>
                            <input type="number" class="form-control" placeholder="Enter here..." name="per_day_limit"
                                value="{{ $data->per_day_limit }}">
                            @if ($errors->has('per_day_limit'))
                                <span class="help-block">
                                    <span class="text-danger">{{ $errors->first('per_day_limit') }}</span>
                                </span>
                            @endif
                        </div>
                        <div class="form-group col-lg-6">
                            @php
                                $countries = getCountry();
                                $blocked_country = json_decode($data->blocked_country);
                                if ($blocked_country == null) {
                                    $blocked_country = [];
                                }
                            @endphp
                            <label for="text">Blocked Countries</label>
                            <select name="blocked_country[]" multiple="multiple" data-size="7" data-live-search="true"
                                class="select2 btn-primary fill_selectbtn_in own_selectbox" data-width="100%"
                                id="blocked_country">
                                @if ($countries)
                                    @foreach ($countries as $key => $value)
                                        <option value="{{ $key }}"
                                            {{ in_array($key, $blocked_country) ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @if ($errors->has('blocked_country'))
                                <span class="help-block">
                                    <span class="text-danger">
                                        {{ $errors->first('blocked_country') }}
                                    </span>
                                </span>
                            @endif
                        </div>

                        <div class="form-group col-lg-6">
                            <label for="bank_id">Select Bank</label>
                            {!! Form::select('bank_id', ['' => '-- Select Bank --'] + $bank, null, [
                                'class' => 'form-control select2',
                                'id' => 'bank_id',
                            ]) !!}
                            @if ($errors->has('bank_id'))
                                <span class="text-danger help-block form-error">
                                    <span>{{ $errors->first('bank_id') }}</span>
                                </span>
                            @endif
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="">Descriptor</label>
                            <input type="text" class="form-control" placeholder="Enter here..." name="descriptor"
                                value="{{ $data->descriptor }}">
                            @if ($errors->has('descriptor'))
                                <span class="help-block"> <span
                                        class="text-danger">{{ $errors->first('descriptor') }}</span> </span>
                            @endif
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="text">MID Type</label> <span class="text-danger">*</span></label>
                            {!! Form::select(
                                'mid_type',
                                [
                                    '' => '-- Select MID Type --',
                                    '1' => 'Card',
                                    '2' => 'Bank',
                                    '3' => 'Crypto',
                                    '4' => 'UPI',
                                    '5' => 'APM',
                                ],
                                [isset($data->mid_type) ? $data->mid_type : null],
                                [
                                    'class' => 'form-control select2',
                                    'id' => 'mid_type',
                                    'disabled' => true,
                                ],
                            ) !!}
                            @if ($errors->has('mid_type'))
                                <span class="text-danger help-block form-error">
                                    <span>{{ $errors->first('mid_type') }}</span>
                                </span>
                            @endif
                        </div>
                        @php
                            
                            $acceptedIndustries = json_decode($data->accepted_industries);
                            if ($acceptedIndustries == null) {
                                $acceptedIndustries = [];
                            }
                            
                        @endphp
                        <div class="form-group col-lg-6">
                            <label for="text">Select Industry</label> <span class="text-danger">*</span></label>
                            <select class="form-control select2" name="accepted_industries[]" id="acceptedIndustries"
                                multiple>
                                <option value=""> -- Select Industry --</option>
                                @foreach ($industries as $industry)
                                    <option value="{{ $industry->id }}"
                                        {{ in_array($industry->id, $acceptedIndustries) ? 'selected' : '' }}>
                                        {{ $industry->name }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('accepted_industries'))
                                <span class="text-danger help-block form-error">
                                    <span>{{ $errors->first('accepted_industi') }}</span>
                                </span>
                            @endif
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="">Card Required</label>
                            <div class="basic-form">
                                <div class="form-group mb-0">
                                    <div class="form-check">
                                        <input type="radio" id="rdo-7" name="is_card_required"
                                            class="checkradio form-check-input" value="0"
                                            {{ $data->is_card_required == '0' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="rdo-7">No</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" id="rdo-8" name="is_card_required"
                                            class="checkradio form-check-input" value="1"
                                            {{ $data->is_card_required == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="rdo-8">Yes</label>
                                    </div>
                                </div>
                            </div>
                            @if ($errors->has('is_card_required'))
                                <span class="help-block">
                                    <span class="text-danger">{{ $errors->first('is_card_required') }}</span>
                                </span>
                            @endif
                        </div>
                        {{-- Toggle APM MDR and type --}}
                        <div class="row apmMdrTypeDiv" style="display: none;">
                            <div class="col-lg-6">
                                <label>Minimum MDR %</label>
                                <input type="text" name="apm_mdr" class="form-control apmMdr"
                                    placeholder="Enter minimum MDR" value="{{ $data->apm_mdr }}" />
                                <span class="apm_mdr_error text-danger"></span>
                            </div>
                            <div class="col-lg-6">
                                <label>APM Type</label>
                                <select name="apm_type" class="form-control apmType" disabled>
                                    <option value="">-- Select APM Type --</option>
                                    @foreach (config('custom.midType') as $key => $item)
                                        <option value="{{ $key }}"
                                            {{ $data->apm_type == $key ? 'selected' : '' }}>{{ $item }}</option>
                                    @endforeach
                                </select>
                                <span class="apm_type_error text-danger"></span>
                            </div>
                        </div>
                        <div class="form-group col-lg-12 mt-1">
                            <button type="button" class="btn btn-primary midFormSubmitBtn">Submit</button>
                            <a href="{{ url('superintendent/mid-feature-management') }}" class="btn btn-danger">Cancel</a>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('customScript')
    <script type="text/javascript">
        $(document).ready(function() {
            // * On page load check if selected mid type is APM to show their boxs
            var midTypeValue = $("#mid_type").val();
            if (midTypeValue == "5") {
                $(".apmMdrTypeDiv").show()
            }

            $('#blocked_country').select2({
                placeholder: "-- Select Blocked Country --",
                allowClear: true
            });
            $("#acceptedIndustries").select2({
                placeholder: "-- Select Industries --",
                allowClear: true
            })
            $('body').on('change', '.md-radiobtn', function() {
                var status = $(this).val();
                if (status == 1) {
                    $('#AssignGatewayMID').attr('disabled', false);
                    $('#SelectGatewayMID').attr('disabled', false);
                } else {
                    $('#AssignGatewayMID').attr('disabled', 'disabled');
                    $('#SelectGatewayMID').attr('disabled', 'disabled');
                }
            });

            // Get Sub MID From Main MID
            // $('#SelectGatewayMID').on('change', function() {
            //     id = this.value;
            //     $.ajax({
            //         type: 'POST',
            //         url: "{{ URL::route('getsubmid') }}",
            //         data: {
            //             '_token': $('input[name=_token]').val(),
            //             'id': id
            //         },
            //         beforeSend: function() {
            //             $('#AssignGatewayMID').attr('disabled', 'disabled');
            //         },
            //         success: function(data) {
            //             $('#AssignGatewayMID').html(data.html);
            //             $('#AssignGatewayMID').attr('disabled', false);
            //         },
            //     });
            // });

            // * Listen the MID type changes
            $('#mid_type').on('change', function() {
                var selectedValue = $(this).val();
                if (selectedValue == "5") {
                    $(".apmMdrTypeDiv").show(300)
                } else {
                    $(".apmMdr").val("")
                    $(".apmType").val("")
                    $(".apmMdrTypeDiv").hide(300)
                }
            });
        });

        // * Submit the form
        $(document).on('click', '.midFormSubmitBtn', function() {
            var isValid = true;
            var midType = $("#mid_type").val();
            if (midType == "5") {
                var apmType = $(".apmType").val()
                var apmMdr = $(".apmMdr").val()

                if (apmType == "" || apmType == null || apmType == undefined) {
                    isValid = false;
                    $(".apm_type_error").text("this field is required.")
                } else {
                    isValid = true;
                    $(".apm_type_error").text("")
                }

                if (apmMdr == "" || apmMdr == null || apmMdr == undefined) {
                    isValid = false;
                    $(".apm_mdr_error").text("this field is required.")
                } else if (apmMdr <= 0) {
                    isValid = false;
                    $(".apm_mdr_error").text("Apm minimum MDR should be greater than 0.")
                } else {
                    isValid = true;
                    $(".apm_mdr_error").text("")
                }
            }

            if (isValid) {
                $(this).find('input:text').each(function() {
                    $(this).val($.trim($(this).val()));
                });
                $('#mid-form').submit();
            }


        });
    </script>
@endsection
