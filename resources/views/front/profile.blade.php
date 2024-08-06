@extends('layouts.user.default')

@section('title')
Settings
@endsection

@section('breadcrumbTitle')
<nav aria-label="breadcrumb">
   <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
      <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('dashboardPage') }}">Dashboard</a></li>
      <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Edit Profile</li>
   </ol>
   <h6 class="font-weight-bolder mb-0">Edit Profile</h6>
</nav>

@endsection

@section('content')
<div class="col-xxl-8">
    <div class="card">
        <div class="card-header">
            <h5>Edit Profile</h5>
        </div>

        {{ Form::model($data, ['route' => ['update-user-profile', $data->id], 'method' => 'patch','id'=>'profile-form', 'class'=>'form-dark']) }}
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-6">
                        <label for="basicInput" class="form-label">Name</label>
                        <input class="form-control" type="text" name="name" placeholder="Enter Name" value="{{$data->name}}">
                        @if ($errors->has('name'))
                            <span class="help-block text-danger">
                                {{ $errors->first('name') }}
                            </span>
                        @endif
                    </div>
                    <div class="col-sm-6">
                        <label for="basicInput" class="form-label">Email</label>
                        <input class="form-control" type="email" name="email" placeholder="Enter Email" value="{{$data->email}}">
                        @if ($errors->has('email'))
                            <span class="help-block text-danger">
                                {{ $errors->first('email') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-footer text-end">    
                <a href="javascript:;" class="btn btn-danger mt-3">Cancel</a>
                <button type="submit" class="btn btn-primary mt-3">Update </button>
            </div>
        {{ Form::close() }}
    </div>
</div>
<div class="row">      
    <div class="col-xl-6 col-lg-12">
        
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
