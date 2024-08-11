@extends($agentUserTheme)
@section('title')
    Update Password
@endsection

@section('breadcrumbTitle')
<nav aria-label="breadcrumb">
   <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
      <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('rp.dashboard') }}">Dashboard</a></li>
      <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Update Password</li>
   </ol>
   <h6 class="font-weight-bolder mb-0">Update Password</h6>
</nav>
@endsection
@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">Update Password</h4>
                    </div>
                </div>
                <div class="card-body">
                    {{ Form::model($data, ['route' => ['rp-password-update'], 'method' => 'post', 'class' => 'form-dark']) }}
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <input class="form-control" type="password" placeholder="New Password" name="password">
                            @if ($errors->has('password'))
                                <span class="help-block">
                                    <strong class="text-danger">{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group col-lg-6">
                            <input class="form-control" type="password" placeholder="Confirm Password" name="password_confirmation">
                            @if ($errors->has('password_confirmation'))
                                <span class="help-block">
                                    <strong class="text-danger">{{ $errors->first('password_confirmation') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group col-lg-12 text-end">
                            <a href="{{ route('rp.dashboard') }}" class="btn btn-danger ">Cancel</a>
                            <button type="submit" class="btn btn-primary "> Update </button>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection
