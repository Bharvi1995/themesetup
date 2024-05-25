@extends('layouts.user.default')

@section('title')
Settings
@endsection

@section('breadcrumbTitle')
<a href="{{ route('dashboardPage') }}">Dashboard</a> / Edit Settings
@endsection

@section('content')
@if (!empty($data->api_key))
        <div class="row">
            <div class="col-md-6">
                <h4 class="mt-50">Account Setting</h4>
            </div>
            <div class="col-xl-12">
                <div class="card mt-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-1 col-xxl-1 text-center">
                                <i class="fa fa-user-secret text-primary" style="font-size: 56px;"></i>
                            </div>
                            <div class="col-xl-11 col-xxl-11">
                                <h4>
                                    Secret Key <br>
                                    <span class="badge badge-warning mt-1" id="link"
                                        data-link="{{ $data->api_key }}">{{ $data->api_key }}</span>
                                    <span class="btn btn-primary btn-sm" id="CopyButton" style="cursor: pointer;">Copy</span>

                                </h4>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
<div class="row">      
    <div class="col-xl-6 col-lg-12">
        {{ Form::model($data, ['route' => ['update-user-profile', $data->id], 'method' => 'patch','id'=>'profile-form', 'class'=>'form-dark']) }}
        <div class="card">
            <div class="card-header">
                <div class="header-title">
                    <h4 class="card-title">Personal Info</h4>
                </div>
            </div>

            <div class="card-body">
                <div class="basic-form mb-2">
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label> Name</label>
                            <input class="form-control" type="text" name="name" placeholder="Enter Name" value="{{$data->name}}">
                            @if ($errors->has('name'))
                                <span class="help-block text-danger">
                                    {{ $errors->first('name') }}
                                </span>
                            @endif
                        </div>
                        <div class="form-group col-md-12">
                            <label>Email</label>
                            <input class="form-control" type="email" name="email" placeholder="Enter Email" value="{{$data->email}}">
                            @if ($errors->has('email'))
                                <span class="help-block text-danger">
                                    {{ $errors->first('email') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                
            </div> 
            <div class="card-footer text-end">
                <a href="javascript:;" class="btn btn-danger">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Changes </button>
            </div>       
        </div>
        {{ Form::close() }}
    </div> 
    <div class="col-sm-6">
        <div class="card">
            <div class="card-header">
                <div class="header-title">
                    <h4 class="card-title">Update Password</h4>
                </div>
            </div>
            <form action="{{ route('change-user-pass') }}" method="post" class="form-dark">
                {{ csrf_field() }}
                <div class="card-body">
                    <div class="basic-form mb-2">
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label> New Password</label>
                                <div class="input-group input-group-flat">
                                    <input class="form-control" type="password" placeholder="Enter here" name="password" id="password">
                                    <span class="input-group-text" id="togglePassword">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>
                                    </span>
                                </div>
                                @if ($errors->has('password'))
                                    <span class="help-block text-danger">
                                        {{ $errors->first('password') }}
                                    </span>
                                @endif
                            </div>
                            <div class="form-group col-md-12">
                                <label>Email</label>
                                <label class="form-label">Confirm Password</label>
                                <div class="input-group input-group-flat">
                                    <input class="form-control" type="password" placeholder="Enter here" name="password_confirmation" id="password_confirmation">
                                    <span class="input-group-text" id="toggleConfirmPassword">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary"> Update Password </button>
                </div>
            </form>
        </div>
    </div>       
</div>
@endsection
@section('customScript')
    <script src="{{ storage_asset('themeAdmin/custom_js/front/profile.js') }}"></script>
    <script>
        function Clipboard_CopyTo(value) {
            var tempInput = document.createElement("input");
            tempInput.value = value;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand("copy");
            document.body.removeChild(tempInput);
        }
        document.querySelector('#CopyButton').onclick = function() {
            var code = $('#link').attr("data-link");
            Clipboard_CopyTo(code);
            toastr.success("Your account secret key has been copied successfully.");
        }

        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
        togglePassword.addEventListener('click', function (e) {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            if(type === 'text') {
                document.getElementById("togglePassword").innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye-off" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M10.585 10.587a2 2 0 0 0 2.829 2.828" /><path d="M16.681 16.673a8.717 8.717 0 0 1 -4.681 1.327c-3.6 0 -6.6 -2 -9 -6c1.272 -2.12 2.712 -3.678 4.32 -4.674m2.86 -1.146a9.055 9.055 0 0 1 1.82 -.18c3.6 0 6.6 2 9 6c-.666 1.11 -1.379 2.067 -2.138 2.87" /><path d="M3 3l18 18" /></svg>';
            } else {
                document.getElementById("togglePassword").innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>'
            }
            
        });


        const toggleConfirmPassword = document.querySelector('#toggleConfirmPassword');
        const password_confirmation = document.querySelector('#password_confirmation');

        toggleConfirmPassword.addEventListener('click', function (e) {
            const type = password_confirmation.getAttribute('type') === 'password' ? 'text' : 'password';
            password_confirmation.setAttribute('type', type);
            if(type === 'text') {
                document.getElementById("toggleConfirmPassword").innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye-off" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M10.585 10.587a2 2 0 0 0 2.829 2.828" /><path d="M16.681 16.673a8.717 8.717 0 0 1 -4.681 1.327c-3.6 0 -6.6 -2 -9 -6c1.272 -2.12 2.712 -3.678 4.32 -4.674m2.86 -1.146a9.055 9.055 0 0 1 1.82 -.18c3.6 0 6.6 2 9 6c-.666 1.11 -1.379 2.067 -2.138 2.87" /><path d="M3 3l18 18" /></svg>';
            } else {
                document.getElementById("toggleConfirmPassword").innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>'
            }
            
        });
    </script>
@endsection
