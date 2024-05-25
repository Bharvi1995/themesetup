<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ config('app.name') }} | {{ __('messages.transactionSccess') }}</title>
    <link rel="shortcut icon" href="{{ storage_asset('NewTheme/images/favicon.ico') }}" />
    <link rel="stylesheet" href="{{ storage_asset('themeAdmin/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ storage_asset('themeAdmin/css/typography.css') }}">
    <link rel="stylesheet" href="{{ storage_asset('themeAdmin/css/style.css') }}">
    <link rel="stylesheet" href="{{ storage_asset('themeAdmin/css/responsive.css') }}">
    <link href="{{ storage_asset('themeAdmin/css/custom.css') }}" rel="stylesheet">
</head>

<body>
    <div class="mt-5 iq-maintenance">
        <div class="container-fluid p-0">
            <div class="row no-gutters">
                <div class="col-sm-12 text-center">
                    <div class="iq-maintenance">
                        <img src="{{ storage_asset('NewTheme/images/Logo.png') }}" alt="" width="300px">
                        <h3 class="mt-4 mb-1">{{ __('messages.headingTest') }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="container mt-3">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card iq-mb-3">
                        <div class="card-body">
                            <div class="m-3">
                                <h4 class="card-title text-center">{{ __('messages.transactionSccess') }}</h4>
                                <p class="card-text text-center text-success">{{ $input['reason'] }}</p>
                            </div>
                            <a href="{{ $redirect_url }}"
                                class="m-1 btn btn-success btn-block">{{ __('messages.returnMerchantSite') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ storage_asset('themeAdmin/js/jquery-latest.min.js') }}"></script>
    <script src="{{ storage_asset('themeAdmin/js/popper.min.js') }}"></script>
    <script src="{{ storage_asset('themeAdmin/js/bootstrap.min.js') }}"></script>
    <script src="{{ storage_asset('themeAdmin/js/jquery.appear.js') }}"></script>
    <script src="{{ storage_asset('themeAdmin/js/countdown.min.js') }}"></script>
    <script src="{{ storage_asset('themeAdmin/js/waypoints.min.js') }}"></script>
    <script src="{{ storage_asset('themeAdmin/js/jquery.counterup.min.js') }}"></script>
    <script src="{{ storage_asset('themeAdmin/js/wow.min.js') }}"></script>
    <script src="{{ storage_asset('themeAdmin/js/apexcharts.js') }}"></script>
    <script src="{{ storage_asset('themeAdmin/js/slick.min.js') }}"></script>
    <script src="{{ storage_asset('themeAdmin/js/select2.min.js') }}"></script>
    <script src="{{ storage_asset('themeAdmin/js/owl.carousel.min.js') }}"></script>
    <script src="{{ storage_asset('themeAdmin/js/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ storage_asset('themeAdmin/js/smooth-scrollbar.js') }}"></script>
    <script src="{{ storage_asset('themeAdmin/js/lottie.js') }}"></script>
    <script src="{{ storage_asset('themeAdmin/js/chart-custom.js') }}"></script>
    <script src="{{ storage_asset('themeAdmin/js/custom.js') }}"></script>
</body>

</html>
