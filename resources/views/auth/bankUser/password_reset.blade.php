<!doctype html>
<html class="loading dark-layout" lang="en" data-textdirection="ltr">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <title>{{ config('app.name') }} | Bank Reset Password</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ storage_asset('NewTheme/images/favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css"
        integrity="sha512-5A8nwdMOWrSz20fDsjczgUidUBR8liPYU+WymTZP1lmY9G6Oc7HlZv156XqnsgNUzTyMefFTcsFH/tnJE/+xBg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- BEGIN: Theme CSS-->

    <link rel="stylesheet" type="text/css" href="{{ storage_asset('NewTheme/css/bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('NewTheme/css/bootstrap-extended.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('NewTheme/css/auth.css') }}">
</head>

<body>
    <!-- loader Start -->
    <div id="loading">
        <p class="mt-1">Loading...</p>
    </div>
    <!-- loader END -->
    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="container">
            <div class="row m-0">
                <div class="col-md-4 col-xl-4 col-xxl-4 offset-md-4 offset-xl-4 offset-xxl-4 content-body">
                    <div class="row content-box-form">
                        <div class="col-md-12 text-center mb-2">
                            <img src="{{ storage_asset('NewTheme/images/Logo.png') }}" width="260px">
                        </div>
                        <div class="col-md-12 form-contant-right">
                            <h3>Reset Password</h3>
                            <form action="{{ route('bank-password-resetForm') }}" method="post" id="form" class="mt-1">
                                {!! csrf_field() !!}
                                <input type="hidden" name="token" value="{{ $token }}">

                                @if(\Session::get('active'))
                                <div class="alert alert-success fade show" role="alert">
                                    {{ $errors->first('active') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </div>
                                @endif
                                @if(\Session::get('success'))
                                <div class="alert alert-success fade show" role="alert">
                                    {{ \Session::get('success') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </div>
                                @endif
                                {{ \Session::forget('success') }}
                                @if(\Session::get('error'))
                                <div class="alert alert-danger fade show" role="alert">
                                    {{ \Session::get('error') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </div>
                                @endif

                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="text" class="form-control" placeholder="Enter here" name="email"
                                        value="{{ request()->get('email') }}" readonly>
                                    @if ($errors->has('email'))
                                        <span class="text-danger">
                                            {{ $errors->first('email') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group mt-1">
                                    <label>Password</label>
                                    <div class="input-group rounded-input form-password-toggle">
                                        <input type="password" class="form-control" placeholder="Enter here" name="password" autocomplete="off" id="password">
                                        <span id="pwd-show" class="input-group-text cursor-pointer"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye font-small-4"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></span>
                                    </div>
                                    @if ($errors->has('password'))
                                        <span class="text-danger">
                                            {{ $errors->first('password') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="form-group mt-1">
                                    <label>Confirm Password</label>
                                    <div class="input-group rounded-input form-password-toggle">
                                        <input type="password" class="form-control" placeholder="Enter here" name="password_confirmation" autocomplete="off" id="cnf-password">
                                        <span id="cnf-pwd-show" class="input-group-text cursor-pointer"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye font-small-4"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></span>
                                    </div>
                                    @if ($errors->has('password_confirmation'))
                                        <span class="text-danger">
                                            {{ $errors->first('password_confirmation') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="text-center">
                                    <button class="btn btn-danger w-100 g-recaptcha mt-2"
                                        data-sitekey="{{ config('app.captch_sitekey') }}" data-callback='onSubmit'
                                        data-action='submit'>Reset Password</button>
                                </div>
                            </form>
                        </div>

                        <div class="col-md-12 mt-2">
                            <p class="text-center">Back To <a href="{{ route('bank/login') }}" class="text-primary"> Sign In
                                </a> </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END: Content-->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.js'></script>
    <script src="https://www.google.com/recaptcha/api.js"></script>

    <script src="{{ storage_asset('NewTheme/vendors/js/forms/select/select2.full.min.js') }}"></script>
    <script src="{{ storage_asset('NewTheme/js/form-select2.js') }}"></script>

    <script>
        $('#pwd-show').on('click', function () {
            var pwd = $('#password').attr('type');
            if(pwd == 'password'){
                $('#password').attr('type', 'text');
            }else{
                $('#password').attr('type', 'password');
            }
        });
        $('#cnf-pwd-show').on('click', function () {
            var pwd = $('#cnf-password').attr('type');
            if(pwd == 'password'){
                $('#cnf-password').attr('type', 'text');
            }else{
                $('#cnf-password').attr('type', 'password');
            }
        });
        jQuery(document).ready(function() {
            jQuery("#load").fadeOut();
            jQuery("#loading").delay().fadeOut("");
        });
    </script>
    <script>
        function onSubmit(token) {
            document.getElementById("form").submit();
        }
    </script>
</body>

</html>