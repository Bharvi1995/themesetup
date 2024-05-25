<!DOCTYPE html>
<html class="loading dark-layout" lang="en" data-textdirection="ltr">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <title>{{ config('app.name') }} | {{ __('messages.pageTitle') }}</title>
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

        .langDropdown {
            background: #ffffff !important;
        }

        .langDropdown li:hover {
            background-color: var(--primary-1) !important;

        }

        .langDropdown .dropdown-item:hover {
            color: #ffffff !important;
        }

        .imgPayment{
            width:260px !important;
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

                <div class="col-md-8 col-xl-8 col-xxl-8 offset-2">
                    <div class="row text-center">
                        <img src="{{ storage_asset('NewTheme/images/Logo.png') }}" class="imgPayment" width="260px">
                    </div>
                    <div class="row">
                        <div class="d-flex justify-content-center  align-items-center my-3">
                            <h4 class="text-primary me-3">{{ __('messages.selectMethod') }}</h4>
                            <!-- Language select -->
                            
                        </div>
                    </div>
                    <div class="row match-height">
                        @if (!empty($user->mid) && $user->mid != 0)
                            <div class="col-lg-8">
                                <div class="card">
                                    <div class="card-body gateway-card">
                                        <div class="row">
                                            <div class="col-md-9">
                                                <h4 class="card-title mb-50">
                                                    Make a payment with your card.
                                                </h4>
                                                <p class="card-text">Pay with Card and Dance into the Transaction Groove</p>
                                            </div>
                                            <div class="col-md-3 text-right">
                                                <i class="fa fa-credit-card" style="font-size: 32px;"></i>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-12">
                                                <a href="{{ route('api.v2.card', $transaction_session->order_id) }}"
                                                    class="btn btn-danger w-100">{{ __('messages.payNow') }}</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if (!empty($user->bank_mid) && $user->bank_mid != 0)
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
                                                <a href="{{ route('api.v2.bank', $transaction_session->order_id) }}"
                                                    class="btn btn-danger w-100">{{ __('messages.payNow') }}</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if (!empty($user->crypto_mid) && $user->crypto_mid != 0)
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
                                                <a href="{{ route('api.v2.crypto', $transaction_session->order_id) }}"
                                                    class="btn btn-danger w-100">{{ __('messages.payNow') }}</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if (!empty($user->upi_mid) && $user->upi_mid != 0)
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="card-body gateway-card">
                                        <div class="row">
                                            <div class="col-md-9">
                                                <h4 class="card-title mb-50">
                                                    {{ __('messages.payWithUPI') }}
                                                </h4>
                                                <p class="card-text">{{ __('messages.payWithUPIText') }}</p>
                                            </div>
                                            <div class="col-md-3 text-right">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 466"
                                                    id="upi">
                                                    <path fill="#70706e"
                                                        d="M740.7 305.6h-43.9l61-220.3h43.9l-61 220.3zM717.9 92.2c-3-4.2-7.7-6.3-14.1-6.3H462.6l-11.9 43.2h219.4l-12.8 46.1H481.8v-.1h-43.9l-36.4 131.5h43.9l24.4-88.2h197.3c6.2 0 12-2.1 17.4-6.3 5.4-4.2 9-9.4 10.7-15.6l24.4-88.2c1.9-6.6 1.3-11.9-1.7-16.1zm-342 199.6c-2.4 8.7-10.4 14.8-19.4 14.8H130.2c-6.2 0-10.8-2.1-13.8-6.3-3-4.2-3.7-9.4-1.9-15.6l55.2-198.8h43.9l-49.3 177.6h175.6l49.3-177.6h43.9l-57.2 205.9z">
                                                    </path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-12">
                                                <a href="{{ route('api.v2.upi', $transaction_session->order_id) }}"
                                                    class="btn btn-danger w-100">{{ __('messages.payNow') }}</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
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
