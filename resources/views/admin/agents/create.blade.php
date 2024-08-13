@extends('layouts.admin.default')
@section('title')
    Create Referral Partner
@endsection

@section('breadcrumbTitle')
    <nav aria-label="breadcrumb">
       <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ url('paylaksa/agents') }}">Referral Partners</a></li>
          <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Create</li>
       </ol>
       <h6 class="font-weight-bolder mb-0">Create</h6>
    </nav>
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card  mt-1">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">Create Referral Partner</h4>
                    </div>
                </div>
                <div class="card-body">
                    {!! Form::open(['route' => 'agents.store', 'method' => 'post', 'id' => 'agent-form', 'class' => 'form-dark']) !!}
                    <div class="row">
                        <div class="col-lg-6 form-group">
                            <label for="">Name</label>
                            <input class="form-control" name="name" type="text" placeholder="Enter here..."
                                value="{{ old('name') }}">
                            @if ($errors->has('name'))
                                <span class="help-block">
                                    <span class="text-danger">{{ $errors->first('name') }}</span>
                                </span>
                            @endif
                        </div>
                        <div class="col-lg-6 form-group">
                            <label for="">Email</label>
                            <input class="form-control" name="email" type="email" placeholder="Enter here..."
                                value="{{ old('email') }}" autocomplete="off">
                            @if ($errors->has('email'))
                                <span class="help-block">
                                    <span class="text-danger">{{ $errors->first('email') }}</span>
                                </span>
                            @endif
                        </div>
                        <div class="col-lg-6 form-group">
                            <label for="">Password</label>
                            <input class="form-control" name="password" type="password" placeholder="Enter here..."
                                autocomplete="off">
                            @if ($errors->has('password'))
                                <span class="help-block">
                                    <span class="text-danger">{{ $errors->first('password') }}</span>
                                </span>
                            @endif
                        </div>
                        <div class="col-lg-6 form-group">
                            <label for="">Confirm Password</label>
                            <input class="form-control" name="password_confirmation" type="password"
                                placeholder="Enter here...
                            " autocomplete="off">
                        </div>
                        <div class="col-lg-6 form-group">
                            <label for="">OTP required for Login</label>
                            <div class="form-group mb-0">
                                <label class="form-check-inline mr-3"><input type="radio" id="rdo-1"
                                        name="is_otp_required" value="1" class="form-check-input"
                                        @if (old('is_otp_required') == '1' || old('is_otp_required') == null) checked @endif>
                                    Yes</label>
                                <label class="form-check-inline mr-3"><input type="radio" id="rdo-2"
                                        name="is_otp_required" class="form-check-input" value="0"
                                        @if (old('is_otp_required') == '0' || old('is_otp_required') == null) checked @endif>
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
                                <label class="form-check-inline mr-3"><input type="radio" id="rdo-1"
                                        name="is_wl_merchant_allow" value="1" class="form-check-input"
                                        @if (old('is_wl_merchant_allow') == '1' || old('is_wl_merchant_allow') == null) checked @endif> Yes</label>
                                <label class="form-check-inline mr-3"><input type="radio" id="rdo-2"
                                        name="is_wl_merchant_allow" class="form-check-input" value="0"
                                        @if (old('is_wl_merchant_allow') == '0' || old('is_wl_merchant_allow') == null) checked @endif>
                                    No</label>
                            </div>
                            @if ($errors->has('is_wl_merchant_allow'))
                                <span class="help-block">
                                    <span class="text-danger">{{ $errors->first('is_wl_merchant_allow') }}</span>
                                </span>
                            @endif
                        </div>
                        <div class="col-lg-12 form-group mt-2">
                            <button type="submit" class="btn btn-primary ">Submit</button>
                            <a href="{{ url('paylaksa/agents') }}" class="btn btn-danger ">Cancel</a>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
