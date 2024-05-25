@extends($agentUserTheme)
@section('title')
    Profile
@endsection

@section('breadcrumbTitle')
    Profile
@endsection
@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">Profile Edit</h4>
                    </div>
                </div>
                <div class="card-body">
                    {{ Form::model($data, ['route' => ['rp-profile-update'], 'method' => 'post', 'class' => 'form-dark']) }}
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <input class="form-control" type="text" name="name" placeholder="Enter Name"
                                value="{{ $data->name }}">
                            @if ($errors->has('name'))
                                <span class="help-block">
                                    <strong class="text-danger">{{ $errors->first('name') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group col-lg-6">
                            <input class="form-control" type="email" name="email" placeholder="Enter Email"
                                value="{{ $data->email }}">
                            @if ($errors->has('email'))
                                <span class="help-block">
                                    <strong class="text-danger">{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group col-lg-6">
                            <input class="form-control" type="password" placeholder="New Password" name="password">
                            @if ($errors->has('password'))
                                <span class="help-block">
                                    <strong class="text-danger">{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group col-lg-6">
                            <input class="form-control" type="password" placeholder="Re-type New Password"
                                name="password_confirmation">
                        </div>
                        <div class="form-group col-lg-12 text-end">
                            <a href="{{ route('rp.dashboard') }}" class="btn btn-danger ">Cancel</a>
                            <button type="submit" class="btn btn-primary "> Submit </button>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection
