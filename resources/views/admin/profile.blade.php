@extends('layouts.admin.default')

@section('title')
    Edit Profile
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('dashboardPage') }}">Dashboard</a> / Edit Profile
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-6">
            <div class="card">
                <div class="card-header">
                    <div class="header-title">
                        <h4 class="card-title">Personal Info</h4>
                    </div>
                </div>
                <div class="card-body">
                    {{ Form::model($data, ['route' => ['update-profile', $data->id], 'method' => 'patch', 'class' => 'form-dark']) }}
                    <div class="form-group">
                        <label>Name</label>
                        <input class="form-control" type="text" name="name" placeholder="Enter Name"
                            value="{{ $data->name }}">
                        @if ($errors->has('name'))
                            <span class="help-block text-danger">
                                {{ $errors->first('name') }}
                            </span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input class="form-control" type="email" name="email" placeholder="Enter Email"
                            value="{{ $data->email }}" {{ !empty($data->token) ? 'disabled' : '' }}>
                        @if ($errors->has('email'))
                            <span class="help-block text-danger">
                                {{ $errors->first('email') }}
                            </span>
                        @endif

                        @if (!empty($data->email_changes))
                            <div class="text-right">
                                <code>Note:-Your email change request has been pending.</code>

                                <a href="{{ route('resend.admin.profile') }}" class="btn btn-danger text-right"> Resend
                                    Mail </a>
                            </div>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-primary">Save Changes </button>
                    {{ Form::close() }}
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="card">
                <div class="card-header">
                    <div class="header-title">
                        <h4 class="card-title">Change Password</h4>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('user-change-pass') }}" method="post" class="form-dark">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>Current Password</label>
                            <input class="form-control" type="password" placeholder="Enter here" name="current_password">
                            @if ($errors->has('current_password'))
                                <span class="help-block text-danger">
                                    {{ $errors->first('current_password') }}
                                </span>
                            @endif
                        </div>
                        <div class="form-group">
                            <label>New Password</label>
                            <input class="form-control" type="password" placeholder="Enter here" name="password">
                            @if ($errors->has('password'))
                                <span class="help-block text-danger">
                                    {{ $errors->first('password') }}
                                </span>
                            @endif
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input class="form-control" type="password" placeholder="Enter here"
                                name="password_confirmation">
                        </div>
                        <button type="submit" class="btn btn-primary"> Change Password </button>
                    </form>
                </div>
            </div>
        </div>
    @endsection
