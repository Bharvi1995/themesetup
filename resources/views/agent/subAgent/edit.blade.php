@extends('layouts.agent.default')

@section('title')
    Edit Sub User
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / <a href="{{ route('sub-rp.index') }}">Sub User</a> / Edit
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-header">

                    <h4 class="card-title">Edit Sub User</h4>
                    <a href="{{ route('sub-rp.index') }}" class="btn btn-primary btn-sm"> <i class="fa fa-arrow-left"
                            aria-hidden="true"></i></a>
                </div>
                <div class="card-body">
                    {{ Form::model($data, ['route' => ['sub-rp.update', $data->id], 'method' => 'patch', 'id' => 'agent-form', 'class' => 'form-dark']) }}
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
                        <div class="col-lg-6 form-group">
                            <label for="">OTP required for Login</label>
                            <div class="form-group mb-0">
                                <label class="radio-inline mr-3"><input type="radio" id="rdo-1" name="is_otp_required"
                                        value="1" class="checkradio"
                                        {{ $data->is_otp_required == 1 ? 'checked' : '' }}>
                                    Yes</label>
                                <label class="radio-inline mr-3"><input type="radio" id="rdo-2" name="is_otp_required"
                                        class="checkradio" value="0"
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
                            <label for="">Status</label>
                            <div class="form-group mb-0">
                                <label class="radio-inline mr-3"><input type="radio" name="is_active" value="1"
                                        class="checkradio" {{ $data->is_active == 1 ? 'checked' : '' }}>
                                    Active</label>
                                <label class="radio-inline mr-3"><input type="radio" name="is_active" class="checkradio"
                                        value="0" {{ $data->is_active == 0 ? 'checked' : '' }}>
                                    Deactive</label>
                            </div>
                            @if ($errors->has('is_active'))
                                <span class="help-block">
                                    <span class="text-danger">{{ $errors->first('is_active') }}</span>
                                </span>
                            @endif
                        </div>
                        <div class="col-lg-12 form-group mt-2">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="{{ route('sub-rp.index') }}" class="btn btn-danger"></i>Cancel</a>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
