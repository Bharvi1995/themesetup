@extends('layouts.admin.default')
@section('title')
    Edit Referral Partner
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / <a href="{{ route('agents.index') }}">Referral Partners</a> /
    Edit
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card  mt-1">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">Edit Referral Partner</h4>
                    </div>
                    <a href="{{ route('agents.index') }}" class="btn btn-primary btn-sm rounded"> <i class="fa fa-arrow-left"
                            aria-hidden="true"></i></a>
                </div>
                <div class="card-body">
                    {{ Form::model($data, ['route' => ['agents.update', $data->id], 'method' => 'patch', 'id' => 'agent-form', 'class' => 'form-dark']) }}
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label for="">Name</label>
                            <input class="form-control" name="name" type="text" placeholder="Enter here..."
                                value="{{ $data->name }}">
                            @if ($errors->has('name'))
                                <span class="help-block">
                                    <span class="text-danger">{{ $errors->first('name') }}</span>
                                </span>
                            @endif
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="">Email</label>
                            <input class="form-control" name="email" type="email" placeholder="Enter here..."
                                value="{{ $data->email }}">
                            @if ($errors->has('email'))
                                <span class="help-block">
                                    <span class="text-danger">{{ $errors->first('email') }}</span>
                                </span>
                            @endif
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="">Password</label>
                                <input class="form-control" name="password" type="password" placeholder="Enter here..."
                                    value="">
                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <span class="text-danger">{{ $errors->first('password') }}</span>
                                    </span>
                                @endif
                                <small>The password must contain: One Upper, Lower, Numeric and Special Character. </small>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="">Confirm Password</label>
                                <input class="form-control" name="confirm_password" type="password"
                                    placeholder="Enter here..." value="">
                                @if ($errors->has('confirm_password'))
                                    <span class="help-block">
                                        <span class="text-danger">{{ $errors->first('confirm_password') }}</span>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="">Add Buy Rate For Visa</label>
                            <input class="form-control" name="add_buy_rate" type="text" placeholder="Enter here..."
                                value="{{ $data->add_buy_rate }}">
                            @if ($errors->has('add_buy_rate'))
                                <span class="help-block">
                                    <span class="text-danger">{{ $errors->first('add_buy_rate') }}</span>
                                </span>
                            @endif
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="">Add Buy Rate For Master Card</label>
                            <input class="form-control" name="add_buy_rate_master" type="text"
                                placeholder="Enter here..." value="{{ $data->add_buy_rate_master }}">
                            @if ($errors->has('add_buy_rate_master'))
                                <span class="help-block">
                                    <span class="text-danger">{{ $errors->first('add_buy_rate_master') }}</span>
                                </span>
                            @endif
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="">Add Buy Rate For Amex Card</label>
                            <input class="form-control" name="add_buy_rate_amex" type="text" placeholder="Enter here..."
                                value="{{ $data->add_buy_rate_amex }}">
                            @if ($errors->has('add_buy_rate_amex'))
                                <span class="help-block">
                                    <span class="text-danger">{{ $errors->first('add_buy_rate_amex') }}</span>
                                </span>
                            @endif
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="">Add Buy Rate For Discover Card</label>
                            <input class="form-control" name="add_buy_rate_discover" type="text"
                                placeholder="Enter here..." value="{{ $data->add_buy_rate_discover }}">
                            @if ($errors->has('add_buy_rate_discover'))
                                <span class="help-block">
                                    <span class="text-danger">{{ $errors->first('add_buy_rate_discover') }}</span>
                                </span>
                            @endif
                        </div>
                        <div class="col-lg-6 form-group">
                            <label for="">OTP required for Login</label>
                            <div class="form-group mb-0">
                                <label class="form-check-label mr-3"><input type="radio" id="rdo-1"
                                        name="is_otp_required" value="1" class="form-check-input"
                                        {{ $data->is_otp_required == 1 ? 'checked' : '' }}>
                                    Yes</label>
                                <label class="form-check-label mr-3"><input type="radio" id="rdo-2"
                                        name="is_otp_required" class="form-check-input" value="0"
                                        {{ $data->is_otp_required == 0 ? 'checked' : '' }}>
                                    No</label>
                            </div>
                            @if ($errors->has('is_otp_required'))
                                <span class="help-block">
                                    <span class="text-danger">{{ $errors->first('is_otp_required') }}</span>
                                </span>
                            @endif
                        </div>
                        <div class="col-lg-6 form-group">
                            <label for="">Can create white label merchant?</label>
                            <div class="form-group mb-0">
                                <label class="form-check-label mr-3"><input type="radio" id="rdo-1"
                                        name="is_wl_merchant_allow" value="1" class="form-check-input"
                                        {{ $data->is_wl_merchant_allow == 1 ? 'checked' : '' }}>
                                    Yes</label>
                                <label class="form-check-label mr-3"><input type="radio" id="rdo-2"
                                        name="is_wl_merchant_allow" class="form-check-input" value="0"
                                        {{ $data->is_wl_merchant_allow == 0 ? 'checked' : '' }}>
                                    No</label>
                            </div>
                            @if ($errors->has('is_wl_merchant_allow'))
                                <span class="help-block">
                                    <span class="text-danger">{{ $errors->first('is_wl_merchant_allow') }}</span>
                                </span>
                            @endif
                        </div>
                        <div class="col-lg-12 mt-2 form-group">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="{{ url('admin/agents') }}" class="btn btn-danger"></i>Cancel</a>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
