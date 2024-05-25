@extends('layouts.agent.default')

@section('title')
    Create Sub User
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / <a href="{{ route('sub-rp.index') }}">Sub User</a> / Create
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">Create Sub User</h4>
                    </div>
                    <a href="{{ route('sub-rp.index') }}" class="btn btn-primary btn-sm"> <i class="fa fa-arrow-left"
                            aria-hidden="true"></i></a>
                </div>
                <div class="card-body">
                    {!! Form::open(['route' => 'sub-rp.store', 'method' => 'post', 'id' => 'agent-form', 'class' => 'form-dark']) !!}
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
                                <label class="radio-inline mr-3"><input type="radio" id="rdo-1" name="is_otp_required"
                                        value="1" class="checkradio" @if (old('is_otp_required') == '1' || old('is_otp_required') == null) checked @endif>
                                    Yes</label>
                                <label class="radio-inline mr-3"><input type="radio" id="rdo-2" name="is_otp_required"
                                        class="checkradio" value="0" @if (old('is_otp_required') == '0' || old('is_otp_required') == null) checked @endif>
                                    No</label>
                            </div>
                            @if ($errors->has('is_otp_required'))
                                <span class="help-block">
                                    <span class="text-danger">{{ $errors->first('is_otp_required') }}</span>
                                </span>
                            @endif
                        </div>
                        <div class="col-lg-12 form-group mt-2">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="{{ route('sub-rp.index') }}" class="btn btn-danger">Cancel</a>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
