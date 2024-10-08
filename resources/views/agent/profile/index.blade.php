@extends($agentUserTheme)
@section('title')
    Profile Edit
@endsection

@section('breadcrumbTitle')
<nav aria-label="breadcrumb">
   <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
      <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('rp.dashboard') }}">Dashboard</a></li>
      <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Profile Edit</li>
   </ol>
   <h6 class="font-weight-bolder mb-0">Profile Edit</h6>
</nav>
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
