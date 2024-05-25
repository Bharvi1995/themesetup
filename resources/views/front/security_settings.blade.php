@extends('layouts.user.default')

@section('title')
Settings
@endsection

@section('breadcrumbTitle')
<a href="{{ route('dashboardPage') }}">Dashboard</a> / Security
@endsection

@section('content')
<div class="row">
    <div class="col-xl-6 col-lg-12">
        <form action="{{ route('user-change-password') }}" method="post" id="password-form" class="form-dark">
        {{ csrf_field() }}
        <div class="card">
            <div class="card-header">
                <div class="header-title">
                    <h4 class="card-title">Change Password</h4>
                </div>
            </div>

            <div class="card-body">
                <div class="basic-form">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Current Password</label>
                            <input class="form-control" type="password" placeholder="Enter here" name="current_password">
                            @if ($errors->has('current_password'))
                                <span class="help-block text-danger">
                                    {{ $errors->first('current_password') }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>New Password</label>
                            <input class="form-control" type="password" placeholder="Enter here" name="password">
                            @if ($errors->has('password'))
                                <span class="help-block text-danger">
                                    {{ $errors->first('password') }}
                                </span>
                            @endif
                        </div>
                        <div class="form-group col-md-6">
                            <label>Confirm Password</label>
                            <input class="form-control" type="password" placeholder="Enter here" name="password_confirmation">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary"> Change Password </button>
                <a href="javascript:;" class="btn btn-danger">Cancel</a>
            </div>        
        </div>
        </form>
    </div>
    <div class="col-xl-6 col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="header-title">
                    <h4 class="card-title">Two Factor Authentication</h4>
                </div>
            </div>

            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <div class="form-check custom-switch">
                            <input type="checkbox" class="form-check-input" id="is_otp" name="is_otp_required" {{ Auth::user()->is_otp_required?'checked':'' }}>
                            <label class="form-check-label" for="is_otp">Two Factor Authentication</label>
                        </div>
                        @if ($errors->has('is_otp_required'))
                            <span class="help-block text-danger">
                                {{ $errors->first('is_otp_required') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>        
        </div>   
    </div>
</div>
@endsection
@section('customScript')
    <script src="{{ storage_asset('themeAdmin/custom_js/front/profile.js') }}"></script>
    <script type="text/javascript">
        // alert('ada');
        $('body').on('change','#is_otp',function () {
            // alert(is_otp);
            var is_otp = '0';

              // change the value based on check / uncheck
              if ($(this).prop("checked") == true) {
                  var is_otp = '1';
              }

            $.ajax({
              type: 'POST',
              context: $(this),
              url:'{{ route('otp-required') }}',
              data: {
                  '_token': '{{ csrf_token() }}',
                  'is_otp': is_otp
              },
              success: function(data) {
                  if (data.success == true) {
                      toastr.success('Two factor authentication changed successfully!!');
                  } else {
                      toastr.error('Something went wrong!!');
                  }
              },
        });
        })
    </script>
@endsection
