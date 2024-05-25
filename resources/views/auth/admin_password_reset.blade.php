<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ config('app.name') }} | Admin Reset Password</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ storage_asset('NewTheme/images/favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css"
        integrity="sha512-5A8nwdMOWrSz20fDsjczgUidUBR8liPYU+WymTZP1lmY9G6Oc7HlZv156XqnsgNUzTyMefFTcsFH/tnJE/+xBg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- BEGIN: Theme CSS-->

    <link rel="stylesheet" type="text/css" href="{{ storage_asset('NewTheme/css/bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('NewTheme/css/bootstrap-extended.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('NewTheme/css/auth.css') }}">
</head>
</head>

<body>
    <!-- loader Start -->
    <div id="loading">
        <p class="mt-1">Loading...</p>
    </div>
    <!-- loader END -->
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
                            <form action="{{ route('admin-password-resetForm') }}" method="post" id="form"
                                class="mt-1">
                                {!! csrf_field() !!}
                                <input type="hidden" name="token" value="{{ $token }}">

                                @if (session('status'))
                                    <div class="wd-100p alert alert-success fade show" role="alert">
                                        {{ session('status') }}
                                        <button type="button" class="btn-close" data-dismiss="alert"
                                            aria-label="Close">

                                        </button>
                                    </div>
                                @endif

                                <div class="form-group mb-2">
                                    <label>Email</label>
                                    <input type="text" class="form-control" placeholder="Enter here" name="email"
                                        value="{{ request()->get('email') }}" readonly>
                                    @if ($errors->has('email'))
                                        <span class="text-danger">
                                            {{ $errors->first('email') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group mb-2">
                                    <label>Password</label>
                                    <input type="password" class="form-control" placeholder="Enter here" name="password"
                                        autocomplete="off">
                                    @if ($errors->has('password'))
                                        <span class="text-danger">
                                            {{ $errors->first('password') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="form-group mb-2">
                                    <label>Confirm Password</label>
                                    <input type="password" class="form-control" placeholder="Enter here"
                                        name="password_confirmation" autocomplete="off">
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
    <script>
        function onSubmit(token) {
            document.getElementById("form").submit();
        }

        jQuery(document).ready(function() {
            jQuery("#load").fadeOut();
            jQuery("#loading").delay().fadeOut("");
        });
    </script>
</body>

</html>
