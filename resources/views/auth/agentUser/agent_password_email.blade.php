<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ config('app.name') }} | Admin Reset Password</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ storage_asset('setup/images/favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css"
        integrity="sha512-5A8nwdMOWrSz20fDsjczgUidUBR8liPYU+WymTZP1lmY9G6Oc7HlZv156XqnsgNUzTyMefFTcsFH/tnJE/+xBg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- BEGIN: Theme CSS-->

    <link rel="stylesheet" type="text/css" href="{{ storage_asset('setup/css/bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('setup/css/bootstrap-extended.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('setup/css/auth.css') }}">
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
                            <img src="{{ storage_asset('setup/images/Logo.png') }}" width="260px">
                        </div>
                        <div class="col-md-12 form-contant-right">
                            <h3>Reset your password</h3>
                            <p>Please provide your email address, and we will send you a link to reset your password.</p>
                            <form class="mt-4" action="{{ route('rp-password-email') }}"
                                aria-label="{{ __('Reset Password') }}" method="post" id="form">
                                {!! csrf_field() !!}

                                @if (session('status'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <div class="alert-body">
                                            {{ session('status') }}
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                @endif

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
                                    <label>Email</label>
                                    <input type="text" class="form-control mb-0" placeholder="Enter here"
                                        name="email" autocomplete="off">
                                    @if ($errors->has('email'))
                                        <span class="help-block font-red-mint text-danger">
                                            {{ $errors->first('email') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="d-inline-block w-100">
                                    <button class="btn btn-danger w-100 g-recaptcha mt-2"
                                        data-sitekey="{{ config('app.captch_sitekey') }}" data-callback='onSubmit'
                                        data-action='submit'>Send password reset
                                        link</button>
                                </div>
                            </form>
                        </div>

                        <div class="col-md-12 mt-2">
                            <p class="text-center">Back To <a href="{{ route('rp/login') }}" class="text-primary"> Sign
                                    In
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

    <script src="{{ storage_asset('setup/vendors/js/forms/select/select2.full.min.js') }}"></script>
    <script src="{{ storage_asset('setup/js/form-select2.js') }}"></script>

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
