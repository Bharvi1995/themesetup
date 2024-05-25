@extends('layouts.admin.default')

@section('title')
    Create Admin User
@endsection
@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> /<a href="{{ route('admin-user.index') }}">Admin Users</a> /
    Create
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card  mt-1">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">Create Admin User</h4>
                    </div>
                    <a href="{{ route('admin-user.index') }}" class="btn btn-primary btn-sm rounded"> <i
                            class="fa fa-arrow-left" aria-hidden="true"></i></a>
                </div>
                <div class="card-body">
                    {!! Form::open([
                        'route' => 'admin-user.store',
                        'method' => 'post',
                        'id' => 'admin-form',
                        'class' => 'form-dark',
                    ]) !!}
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="">Name</label>
                                <input class="form-control" name="name" type="text" placeholder="Enter here..."
                                    value="{{ old('name') }}">
                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <span class="text-danger">{{ $errors->first('name') }}</span>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="">Email</label>
                                <input class="form-control" name="email" type="email" placeholder="Enter here..."
                                    value="{{ old('email') }}">
                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <span class="text-danger">{{ $errors->first('email') }}</span>
                                    </span>
                                @endif
                            </div>

                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="">Password</label>

                                <input class="form-control" name="password" type="password" placeholder="Enter here..."
                                    value="" autocomplete="off">
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
                                <input class="form-control" name="password_confirmation" type="password"
                                    placeholder="Enter here...
                                " value=""
                                    autocomplete="off">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="">Select Role</label>
                                <select class="select2" name="roles" data-size="7" data-live-search="true"
                                    data-title="--Roles--" id="state_list" data-width="100%">
                                    @if (sizeof($roles) > 0)
                                        <option value="" selected>-- Select Role --</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @if ($errors->has('roles'))
                                    <span class="help-block">
                                        <span class="text-danger">{{ $errors->first('roles') }}</span>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <label for="">Change Status</label>
                            <div class="form-group mb-0">
                                <label class="radio-inline mr-3"><input type="radio" id="rdo-1" name="is_active"
                                        value="1" class="checkradio form-check-input"
                                        @if (old('is_active') == '1' || old('is_active') == null) checked @endif>
                                    Active</label>
                                <label class="radio-inline mr-3"><input type="radio" id="rdo-2" name="is_active"
                                        class="checkradio form-check-input" value="0"
                                        @if (old('is_active') == '0' || old('is_active') == null) checked @endif>
                                    Inactive</label>
                            </div>
                            @if ($errors->has('is_active'))
                                <span class="help-block">
                                    <span class="text-danger">{{ $errors->first('is_active') }}</span>
                                </span>
                            @endif
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="">OTP required for Login</label>
                                <div class="form-group mb-0">
                                    <label class="radio-inline mr-3"><input type="radio" id="rdo-3"
                                            name="is_otp_required" class="checkradio form-check-input" value="1"
                                            @if (old('is_otp_required') == '1' || old('is_otp_required') == null) checked @endif> Yes</label>
                                    <label class="radio-inline mr-3"><input type="radio" id="rdo-4"
                                            name="is_otp_required" class="checkradio form-check-input" value="0"
                                            @if (old('is_otp_required') == '0') checked @endif>
                                        No</label>
                                </div>
                                @if ($errors->has('is_otp_required'))
                                    <span class="help-block">
                                        <span class="text-danger">{{ $errors->first('is_otp_required') }}</span>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-12 mt-3">
                            <button type="submit" class="btn btn-primary ">Submit</button>

                            <a href="{{ url('admin/admin-user') }}" class="btn btn-danger ">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>

    </div>
@endsection
