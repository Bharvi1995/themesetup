<!DOCTYPE html>
<html class="loading dark-layout" lang="en" data-textdirection="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <title>{{ config('app.name') }} |Admin OTP</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ storage_asset('NewTheme/images/favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" integrity="sha512-5A8nwdMOWrSz20fDsjczgUidUBR8liPYU+WymTZP1lmY9G6Oc7HlZv156XqnsgNUzTyMefFTcsFH/tnJE/+xBg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- BEGIN: Theme CSS-->

    <link rel="stylesheet" type="text/css" href="{{ storage_asset('NewTheme/css/bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('NewTheme/css/bootstrap-extended.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('NewTheme/css/auth.css') }}">
</head>
<body>
    <div id="loading">
        <p class="mt-1">Loading...</p>
    </div>
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
                            <h3>OTP</h3>
                            <form action="{{ route('admin.testpay-otp-store') }}" method="post" id="form" class="mt-4">
                                {!! csrf_field() !!}

                                @if (\Session::get('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <div class="alert-body">
                                            {{ \Session::get('success') }}
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                @endif
                                {{ \Session::forget('success') }}
                                @if (\Session::get('error'))
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <div class="alert-body">
                                            {{ \Session::get('error') }}
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                @endif
                                {{ \Session::forget('error') }}

                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="Enter here" name="otp"
                                        autocomplete="off">
                                    @if ($errors->has('otp'))
                                        <span class="help-block font-red-mint text-danger">
                                            {{ $errors->first('otp') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="text-center">
                                    <button class="btn btn-danger w-100 g-recaptcha mt-1"
                                        data-sitekey="{{ config('app.captch_sitekey') }}" data-callback='onSubmit'
                                        data-action='submit'>Login</button>

                                    <a href="{{ route('admin.resend-otp') }}"
                                        class="btn btn-danger mt-1 w-100 disabled" disabled id="resendOTP">Resend OTP
                                        <span id="countdown">60s</span></a>
                                </div>
                            </form>
                        </div>

                        <div class="col-md-12 mt-2">
                            <p class="text-center">Back To <a href="{{ route('admin/login') }}" class="text-primary">
                                    Sign In
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

    <script type="text/javascript">
        var timeLeft = 60;
        var elem = document.getElementById('countdown');
        var timerId = setInterval(countdown, 1000);

        function countdown() {
            document.getElementById("resendOTP").disabled = true;
            document.getElementById("resendOTP").setAttribute("href", "javascript:void(0);");
            if (timeLeft == 0) {
                document.getElementById("resendOTP").disabled = false;
                document.getElementById("resendOTP").setAttribute("href", "{{ route('resend-otp') }}");
                elem.innerHTML = '';
                $('#resendOTP').removeClass('disabled');
            } else {
                elem.innerHTML = timeLeft + 's';
                timeLeft--;
            }
        }
    </script>

    <script>
        jQuery(document).ready(function () {
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
