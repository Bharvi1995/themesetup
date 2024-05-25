<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ config('app.name') }} | White Label Agent Login</title>
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
    <!-- Sign in Start -->


    <!-- Sign in END -->
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="{{ storage_asset('themeAdmin/js/jquery-latest.min.js') }}"></script>
    <script src="{{ storage_asset('themeAdmin/js/popper.min.js') }}"></script>
    <script src="{{ storage_asset('themeAdmin/js/bootstrap.min.js') }}"></script>
    <!-- Appear JavaScript -->
    <script src="{{ storage_asset('themeAdmin/js/jquery.appear.js') }}"></script>
    <!-- Countdown JavaScript -->
    <script src="{{ storage_asset('themeAdmin/js/countdown.min.js') }}"></script>
    <!-- Counterup JavaScript -->
    <script src="{{ storage_asset('themeAdmin/js/waypoints.min.js') }}"></script>
    <script src="{{ storage_asset('themeAdmin/js/jquery.counterup.min.js') }}"></script>
    <!-- Wow JavaScript -->
    <script src="{{ storage_asset('themeAdmin/js/wow.min.js') }}"></script>
    <!-- Apexcharts JavaScript -->
    <script src="{{ storage_asset('themeAdmin/js/apexcharts.js') }}"></script>
    <!-- Slick JavaScript -->
    <script src="{{ storage_asset('themeAdmin/js/slick.min.js') }}"></script>
    <!-- Select2 JavaScript -->
    <script src="{{ storage_asset('themeAdmin/js/select2.min.js') }}"></script>
    <!-- Owl Carousel JavaScript -->
    <script src="{{ storage_asset('themeAdmin/js/owl.carousel.min.js') }}"></script>
    <!-- Magnific Popup JavaScript -->
    <script src="{{ storage_asset('themeAdmin/js/jquery.magnific-popup.min.js') }}"></script>
    <!-- Smooth Scrollbar JavaScript -->
    <script src="{{ storage_asset('themeAdmin/js/smooth-scrollbar.js') }}"></script>
    <!-- Chart Custom JavaScript -->
    <script src="{{ storage_asset('themeAdmin/js/chart-custom.js') }}"></script>
    <!-- Custom JavaScript -->
    <script src="{{ storage_asset('themeAdmin/js/custom.js') }}"></script> <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="container">
            <div class="row m-0">
                <div class="col-md-8 col-xl-8 col-xxl-6 offset-md-2 offset-xl-2 offset-xxl-3 content-body">
                    <div class="row content-box-form">
                        <div class="col-md-4 content-box-left">
                            <img src="{{ storage_asset('NewTheme/images/favicon.ico') }}" class="auth-logo">
                            <h3>Beyond Card Payment</h3>
                            <p>Changing the way you receive and pay.</p>

                            <div class="row">
                                <div class="col-md-12 text-center mt-1">
                                    <img src="{{ storage_asset('NewTheme/images/auth-img.svg') }}" width="150px">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8 form-contant-right">
                            <h3>OTP</h3>
                            <form action="{{ route('wl.rp.testpay-otp-store') }}" method="post" id="form"
                                class="mt-4">
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

                                    <a href="{{ route('wl.rp.resend-otp') }}"
                                        class="btn btn-danger mt-1 w-100 disabled" disabled id="resendOTP">Resend OTP
                                        <span id="countdown">60s</span></a>
                                </div>
                            </form>
                        </div>

                        <div class="col-md-12 mt-2">
                            <p class="text-center">Back To <a href="{{ route('wl/rp/login') }}" class="text-primary">
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

    <script src="{{ storage_asset('NewTheme/vendors/js/forms/select/select2.full.min.js') }}"></script>
    <script src="{{ storage_asset('NewTheme/js/form-select2.js') }}"></script>

    <script type="text/javascript">
        var timeLeft = 60;
        var elem = document.getElementById('countdown');
        var timerId = setInterval(countdown, 1000);

        function countdown() {
            document.getElementById("resendOTP").disabled = true;
            document.getElementById("resendOTP").setAttribute("href", "javascript:void(0);");
            if (timeLeft == 0) {
                document.getElementById("resendOTP").disabled = false;
                document.getElementById("resendOTP").setAttribute("href", "{{ route('wl.rp.resend-otp') }}");
                elem.innerHTML = '';
                $('#resendOTP').removeClass('disabled');
            } else {
                elem.innerHTML = timeLeft + 's';
                timeLeft--;
            }
        }
    </script>
    <script>
        jQuery(document).ready(function() {
            jQuery("#load").fadeOut();
            jQuery("#loading").delay().fadeOut("");
        });

        function onSubmit(token) {
            document.getElementById("form").submit();
        }
    </script>


</body>

</html>
