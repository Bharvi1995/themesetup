<!DOCTYPE html>
<html class="loading dark-layout" lang="en" data-textdirection="ltr">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <title>{{ config('app.name') }} | {{ __('messages.pageTitleTest') }}</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ storage_asset('NewTheme/images/favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css"
        integrity="sha512-5A8nwdMOWrSz20fDsjczgUidUBR8liPYU+WymTZP1lmY9G6Oc7HlZv156XqnsgNUzTyMefFTcsFH/tnJE/+xBg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('NewTheme/css/bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('NewTheme/css/bootstrap-extended.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('NewTheme/css/auth.css') }}">
    <style type="text/css">
        .card {
            background: var(--white);
            border-radius: 0px 0px 3px 3px;
            box-shadow: 0px 2px 5px 0px #05309533;
        }

        .btn-danger {
            background: var(--primary-1) !important;
            border-color: var(--primary-1) !important;
            color: var(--white) !important;
            border-radius: 3px;
        }

        .btn-primary {
            background: var(--primary-3) !important;
            border-color: var(--primary-3) !important;
            color: var(--primary-4) !important;
            border-radius: 3px;
        }

        .langDropdown {
            background: #ffffff !important;
        }

        .langDropdown li:hover {
            background-color: var(--primary-1) !important;

        }

        .langDropdown .dropdown-item:hover {
            color: #ffffff !important;
        }
    </style>
</head>

<body>
    <div id="loading">
        <p class="mt-1">{{ __('messages.loading') }}...</p>
    </div>
    <div class="app-content content">
        <div class="container">
            <div class="row content-body">
                <div class="col-md-9 col-xl-9 col-xxl-9">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <img src="{{ storage_asset('NewTheme/images/Logo.png') }}" alt=""
                                width="250px">
                            <h4 class="text-primary mt-2 mb-1">{{ __('messages.headingTest') }}</h4>
                            <div class="d-flex justify-content-center">
                                <p class="text-primary w-50">{{ __('messages.subHeadingTest') }}</p>

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="d-flex justify-content-center  align-items-center my-3">
                            <h4 class="text-primary me-3">{{ __('messages.selectMethod') }}</h4>
                            <!-- Language change btn -->
                            @include('partials.payment.languageBtn')
                        </div>
                    </div>
                    <div class="row match-height">
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-body gateway-card">
                                    <div class="row">
                                        <div class="col-md-9">
                                            <h4 class="card-title mb-50">
                                                {{ __('messages.payWithCard') }}
                                            </h4>
                                            <p class="card-text">{{ __('messages.payWithCardText') }}</p>
                                        </div>
                                        <div class="col-md-3 text-right">
                                            <i class="fa fa-credit-card" style="font-size: 32px;"></i>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-12">
                                            <a href="{{ route('api.v2.test-card', $transaction_session->order_id) }}"
                                                class="btn btn-danger w-100">{{ __('messages.payNow') }}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-body gateway-card">
                                    <div class="row">
                                        <div class="col-md-9">
                                            <h4 class="card-title mb-50">
                                                {{ __('messages.payWithBank') }}
                                            </h4>
                                            <p class="card-text">{{ __('messages.payWithBankText') }}</p>
                                        </div>
                                        <div class="col-md-3 text-right">
                                            <i class="fa fa-bank" style="font-size: 32px;"></i>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-12">
                                            <a href="{{ route('api.v2.test-bank', $transaction_session->order_id) }}"
                                                class="btn btn-danger w-100">{{ __('messages.payNow') }}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-body gateway-card">
                                    <div class="row">
                                        <div class="col-md-9">
                                            <h4 class="card-title mb-50">
                                                {{ __('messages.payWithCrypto') }}
                                            </h4>
                                            <p class="card-text">{{ __('messages.payWithCryptoText') }}</p>
                                        </div>
                                        <div class="col-md-3 text-right">
                                            <i class="fa fa-btc" style="font-size: 32px;"></i>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-12">
                                            <a href="{{ route('api.v2.test-crypto', $transaction_session->order_id) }}"
                                                class="btn btn-danger w-100">{{ __('messages.payNow') }}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.js'></script>

    <script>
        var url = "{{ route('change.lang') }}"

        jQuery(document).ready(function() {
            jQuery("#load").fadeOut();
            jQuery("#loading").delay().fadeOut("");

            $(document).on("click", ".langListBtn", function() {
                var val = $(this).attr("data-lang");
                window.location.href = url + "?lang=" + val;
            });

        });
    </script>
</body>

</html>
