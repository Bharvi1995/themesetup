@extends('layouts.admin.default')
@section('title')
    Edit Admin User
@endsection
@section('breadcrumbTitle')
    <nav aria-label="breadcrumb">
       <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('admin-user.index') }}">Admin Users</a></li>
          <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Edit</li>
       </ol>
       <h6 class="font-weight-bolder mb-0">Edit</h6>
    </nav>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card  mt-1">
                <div class="card-header">
                    <h4 class="card-title">Edit Admin User</h4>
                </div>
                <div class="card-body">
                    {{ Form::model($data, ['route' => ['admin-user.update', $data->id], 'method' => 'patch', 'id' => 'admin-form', 'class' => 'form-dark']) }}
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="">Name</label>
                                <input class="form-control" name="name" type="text" placeholder="Enter here..."
                                    value="{{ $data->name }}">
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
                                    value="{{ $data->email }}">
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

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="">Select Role</label>
                                <select data-size="7" data-live-search="true" class="form-control" name="roles"
                                    data-title="roles" id="state_list" data-width="100%">
                                    @if (sizeof($roles) > 0)
                                        @foreach ($roles as $role)
                                            @if ($userRole[0] == $role->id)
                                                <option value="{{ $role->id }}" selected>{{ $role->name }}</option>
                                            @else
                                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                                            @endif
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
                            <div class="form-group">
                                <label for="">Change Status</label>
                                <div class="form-group  mb-0">
                                    <label class="form-check-label mr-3"><input type="radio" id="rdo-1"
                                            name="is_active" value="1" class="form-check-input"
                                            {{ $data->is_active == 1 ? 'checked' : '' }}>
                                        Active</label>
                                    <label class="form-check-label mr-3"><input type="radio" id="rdo-2"
                                            name="is_active" class="form-check-input" value="0"
                                            {{ $data->is_active == 0 ? 'checked' : '' }}>
                                        Inactive</label>
                                </div>
                                @if ($errors->has('is_active'))
                                    <span class="help-block">
                                        <span class="text-danger">{{ $errors->first('is_active') }}</span>
                                    </span>
                                @endif
                            </div>

                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="">OTP required for Login</label>
                                <div class="form-group mb-0">
                                    <label class="form-check-label mr-3"><input type="radio" id="rdo-3"
                                            name="is_otp_required" class="form-check-input" value="1"
                                            {{ $data->is_otp_required == 1 ? 'checked' : '' }}> Yes</label>
                                    <label class="form-check-label mr-3"><input type="radio" id="rdo-4"
                                            name="is_otp_required" class="form-check-input" value="0"
                                            {{ $data->is_otp_required == 0 ? 'checked' : '' }}> No</label>
                                </div>
                                @if ($errors->has('is_otp_required'))
                                    <span class="help-block">
                                        <span class="text-danger">{{ $errors->first('is_otp_required') }}</span>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="col-lg-12 mt-3">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <!-- <a href="#" class="yellow-btn mr-3"><i class="fas fa-save me-2"></i>Submit</a> -->
                            <a href="{{ url('paylaksa/admin-user') }}" class="btn btn-danger"></i>Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
@endsection
