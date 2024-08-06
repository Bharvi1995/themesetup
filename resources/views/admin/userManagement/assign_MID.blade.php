@extends('layouts.admin.default')

@section('title')
    MID Management
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / <a href="{{ route('users-management') }}">Merchant Management</a>
    / MID
@endsection

@section('content')
    <style type="text/css">
        .nav-tabs .nav-item {
            margin-bottom: 0px;
        }
    </style>
    <div class="row">
        <div class="col-xl-12">
            <div class="card  mt-1">
                <div class="card-body br-25">
                    <div class="row align-items-center">
                        <div class="col-xl-10 col-xxl-10 mr-auto">
                            <div class="d-sm-flex d-block align-items-center">
                                <i class="fa fa-key text-primary" style="font-size: 56px;"></i>
                                <div class="ms-2">
                                    <h4 class="fs-20 ">API Key</h4>
                                    @if (!isset($data->api_key))
                                        <a href="{{ route('api-key-generate', $data->id) }}"
                                            class="btn btn-success btn-sm">Generate API Key</a>
                                    @else
                                        <p class="fs-14 mb-0 text-danger">{{ $data->api_key }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-xxl-2 text-right">
                            {{-- <a href="{{ route('sendmailforlivemid', $data->id) }}" class="blue-btn me-2"><i class="fas fa-envelope me-2"></i>Send Mail For Live MID</a> --}}
                            <a href="{{ route('users-management') }}" class="btn btn-primary btn-sm"><i
                                    class="fa fa-arrow-left" aria-hidden="true"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card  mt-1">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">Company Name : {{ $data->company_name }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Nav tabs -->
                    <div class="custom-tab-1">
                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('personal-info', $data->id) }}"><i
                                        class="fa fa-hand-o-right me-2"></i> Personal Info</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" href="{{ route('assign-mid', $data->id) }}"><i
                                        class="fa fa-hand-o-right me-2"></i> MID Info</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('card-email-limit', $data->id) }}"><i
                                        class="fa fa-hand-o-right me-2"></i> Card & Email Limit</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('merchant-rate-fee', $data->id) }}"><i
                                        class="fa fa-hand-o-right me-2"></i> Merchant Rate/Fee</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('additional-mail', $data->id) }}"><i
                                        class="fa fa-hand-o-right me-2"></i> Additional Mail Info</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('merchant-rules', $data->id) }}"><i
                                        class="fa fa-hand-o-right me-2"></i> Create Rules</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade show active">
                                <div class="pt-4">
                                    {!! Form::model($data, ['route' => 'assign-mid-store', 'method' => 'post']) !!}
                                    <input type="hidden" name="user_id" value="{{ $data->id }}">
                                    <div class="basic-form">
                                        <div class="row ">
                                            <div class="form-group col-md-6">
                                                <label>Select Main MID <span class="text-danger">*</span></label>
                                                <select class="select2" name="mid" id="mid_id" data-size="7"
                                                    data-live-search="true" data-title="Select MID" data-width="100%">
                                                    <option selected disabled>Select here</option>
                                                    @foreach ($midData as $key => $value)
                                                        <option value="{{ $value->id }}"
                                                            {{ $data->mid == $value->id ? 'selected' : '' }}>
                                                            {{ $value->bank_name }}</option>
                                                    @endforeach
                                                    <option value="0" style="font-weight: 900;"><strong> Remove MID
                                                        </strong></option>
                                                </select>
                                                @if ($errors->has('mid'))
                                                    <span class="text-danger">
                                                        <strong>{{ $errors->first('mid') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row ">
                                            <?php
                                            $arrMultipleMID = [];
                                            if (!empty($data->multiple_mid)) {
                                                $arrMultipleMID = json_decode($data->multiple_mid);
                                            }
                                            $arrMultipleMIDMaster = [];
                                            if (!empty($data->multiple_mid_master)) {
                                                $arrMultipleMIDMaster = json_decode($data->multiple_mid_master);
                                            }
                                            ?>
                                            <div class="form-group col-md-6">
                                                <label>Select Multiple MID for VISA</label>
                                                <select class="select2" name="multiple_mid[]" id="multiple_mid_id"
                                                    data-size="7" data-live-search="true" data-title="Select MID"
                                                    data-width="100%" multiple="multiple">
                                                    <option disabled>Select here</option>
                                                    @foreach ($arrMainVisa as $kn => $vn)
                                                        <option value="{{ $kn }}" selected="">
                                                            {{ $vn }}</option>
                                                    @endforeach
                                                    @foreach ($midListInArray as $kVisa => $vVisa)
                                                        <option value="{{ $kVisa }}">{{ $vVisa }}</option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('multiple_mid'))
                                                    <span class="text-danger">
                                                        <strong>{{ $errors->first('multiple_mid') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Select Multiple MID for Mastercard</label>

                                                <select class="select2 select2Master" name="multiple_mid_master[]"
                                                    id="multiple_mid_master" data-size="7" data-live-search="true"
                                                    data-title="Select MID" data-width="100%" multiple="multiple">
                                                    <option disabled>Select here</option>

                                                    @foreach ($arrMainMaster as $knMaster => $vnMaster)
                                                        <option value="{{ $knMaster }}" selected="">
                                                            {{ $vnMaster }}</option>
                                                    @endforeach
                                                    @foreach ($midListInArrayAll as $kMaster => $vMaster)
                                                        <option value="{{ $kMaster }}">{{ $vMaster }}</option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('multiple_mid_mastercard'))
                                                    <span class="text-danger">
                                                        <strong>{{ $errors->first('multiple_mid_mastercard') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row ">
                                            <div class="form-group col-md-6">
                                                <label>Select Visa MID</label>
                                                <select class="form-control select2" name="visamid">
                                                    <option selected disabled>Select here</option>
                                                    @foreach ($midData as $key => $value)
                                                        <option value="{{ $value->id }}"
                                                            {{ $value->id == $data->visamid ? 'selected' : '' }}>
                                                            {{ $value->bank_name }}</option>
                                                    @endforeach
                                                    <option value="0" style="font-weight: 900;"><strong> Remove MID
                                                        </strong></option>
                                                    <option value="Block" style="font-weight: 900;"
                                                        {{ $data->visamid == 'Block' ? 'selected' : '' }}>Block MID
                                                    </option>
                                                </select>
                                                @if ($errors->has('visamid'))
                                                    <span class="text-danger">
                                                        <strong>{{ $errors->first('visamid') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Select Mastercard MID</label>
                                                <select class="form-control select2" name="mastercardmid">
                                                    <option selected disabled>Select here</option>
                                                    @foreach ($midData as $key => $value)
                                                        <option value="{{ $value->id }}"
                                                            {{ $value->id == $data->mastercardmid ? 'selected' : '' }}>
                                                            {{ $value->bank_name }}</option>
                                                    @endforeach
                                                    <option value="0" style="font-weight: 900;"><strong> Remove MID
                                                        </strong></option>
                                                    <option value="Block" style="font-weight: 900;"
                                                        {{ $data->mastercardmid == 'Block' ? 'selected' : '' }}>Block MID
                                                    </option>
                                                </select>
                                                @if ($errors->has('mastercardmid'))
                                                    <span class="text-danger">
                                                        <strong>{{ $errors->first('mastercardmid') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Select Discover MID</label>
                                                <select class="form-control select2" name="discovermid">
                                                    <option selected disabled>Select here</option>
                                                    @foreach ($midData as $key => $value)
                                                        <option value="{{ $value->id }}"
                                                            {{ $value->id == $data->discovermid ? 'selected' : '' }}>
                                                            {{ $value->bank_name }}</option>
                                                    @endforeach
                                                    <option value="0" style="font-weight: 900;"><strong> Remove MID
                                                        </strong></option>
                                                    <option value="Block" style="font-weight: 900;"
                                                        {{ $data->discovermid == 'Block' ? 'selected' : '' }}>Block MID
                                                    </option>
                                                </select>
                                                @if ($errors->has('discovermid'))
                                                    <span class="text-danger">
                                                        <strong>{{ $errors->first('discovermid') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Select Amex MID</label>
                                                <select class="form-control select2" name="amexmid">
                                                    <option selected disabled>Select here</option>
                                                    @foreach ($midData as $key => $value)
                                                        <option value="{{ $value->id }}"
                                                            {{ $value->id == $data->amexmid ? 'selected' : '' }}>
                                                            {{ $value->bank_name }}</option>
                                                    @endforeach
                                                    <option value="0" style="font-weight: 900;"><strong> Remove MID
                                                        </strong></option>
                                                    <option value="Block" style="font-weight: 900;"
                                                        {{ $data->amexmid == 'Block' ? 'selected' : '' }}>Block MID
                                                    </option>
                                                </select>
                                                @if ($errors->has('amexmid'))
                                                    <span class="text-danger">
                                                        <strong>{{ $errors->first('amexmid') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row ">
                                            <div class="form-group col-md-6">
                                                <label>Select Crypto MID</label>
                                                <select class="form-control select2" name="crypto_mid">
                                                    <option selected disabled>Select here</option>
                                                    @foreach ($cryptoMIDData as $key => $value)
                                                        <option value="{{ $value->id }}"
                                                            {{ $value->id == $data->crypto_mid ? 'selected' : '' }}>
                                                            {{ $value->bank_name }}</option>
                                                    @endforeach
                                                    <option value="0" style="font-weight: 900;"><strong> Remove MID
                                                        </strong></option>
                                                    <option value="Block" style="font-weight: 900;"
                                                        {{ $data->crypto_mid == 'Block' ? 'selected' : '' }}>Block MID
                                                    </option>
                                                </select>
                                                @if ($errors->has('crypto_mid'))
                                                    <span class="text-danger">
                                                        <strong>{{ $errors->first('crypto_mid') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Select Bank MID</label>
                                                <select class="form-control select2" name="bank_mid">
                                                    <option selected disabled>Select here</option>
                                                    @foreach ($bankMIDData as $key => $value)
                                                        <option value="{{ $value->id }}"
                                                            {{ $value->id == $data->bank_mid ? 'selected' : '' }}>
                                                            {{ $value->bank_name }}</option>
                                                    @endforeach
                                                    <option value="0" style="font-weight: 900;"><strong> Remove MID
                                                        </strong></option>
                                                    <option value="Block" style="font-weight: 900;"
                                                        {{ $data->bank_mid == 'Block' ? 'selected' : '' }}>Block MID
                                                    </option>
                                                </select>
                                                @if ($errors->has('bank_mid'))
                                                    <span class="text-danger">
                                                        <strong>{{ $errors->first('bank_mid') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Select UPI MID</label>
                                                <select class="form-control select2" name="upi_mid">
                                                    <option selected disabled>Select here</option>
                                                    @foreach ($upiMIDData as $key => $value)
                                                        <option value="{{ $value->id }}"
                                                            {{ $value->id == $data->upi_mid ? 'selected' : '' }}>
                                                            {{ $value->bank_name }}</option>
                                                    @endforeach
                                                    <option value="0" style="font-weight: 900;"><strong> Remove MID
                                                        </strong></option>
                                                    <option value="Block" style="font-weight: 900;"
                                                        {{ $data->upi_mid == 'Block' ? 'selected' : '' }}>Block MID
                                                    </option>
                                                </select>
                                                @if ($errors->has('upi_mid'))
                                                    <span class="text-danger">
                                                        <strong>{{ $errors->first('upi_mid') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="form-group col-md-12 mt-3">
                                                <button type="submit" class="btn btn-primary">Submit</button>
                                                <a href="{{ url('paylaksa/users-management') }}"
                                                    class="btn btn-danger">Cancel</a>
                                            </div>
                                        </div>
                                    </div>
                                    {!! Form::close() !!}
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
    <script type="text/javascript">
        $(".select2").on("select2:select", function(evt) {
            var element = evt.params.data.element;
            var $element = $(element);

            $element.detach();
            $(this).append($element);
            $(this).trigger("change");
        });

        $(".select2Master").on("select2:select", function(evt) {
            var element = evt.params.data.element;
            var $element = $(element);

            $element.detach();
            $(this).append($element);
            $(this).trigger("change");
        });
    </script>
@endsection
