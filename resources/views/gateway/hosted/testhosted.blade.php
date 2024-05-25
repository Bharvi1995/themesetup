<!DOCTYPE html>
<html class="loading dark-layout" lang="en" data-textdirection="ltr">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <title>{{ config('app.name') }} | {{ __('messages.pageTitleLink') }}</title>
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
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.14/semantic.css">
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

        .cardSelectMain {
            overflow: hidden;
            padding: 15px;
        }

        .cardSelect {
            float: left;
            width: 100px;
            text-align: left;
        }

        [type=radio] {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        [type=radio]+img {
            border: 3px solid #1B1919;
            cursor: pointer;
            width: 80px;
            border-radius: 3px;
            filter: gray;
            -webkit-filter: grayscale(1);
            filter: grayscale(1);
        }

        [type=radio]:checked+img {
            border: 3px solid #1B1919;
            box-shadow: 0px 0px 5px 0px #FFF;
            -webkit-filter: grayscale(0);
            filter: none;
        }

        .ui.attached.segment {
            background: transparent;
            border: unset;
        }

        .ui.icon.input>i.icon {
            color: #FFFFFF;
        }

        .btn-sm {
            font-size: 11px;
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
                <div class="col-md-6 col-xl-6 col-xxl-6">
                    <div class="row">
                        <div class="col-md-12 text-center mb-3">
                            <img src="{{ storage_asset('NewTheme/images/Logo.png') }}" alt=""
                                width="250px">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body gateway-card">
                                    <div class="row">
                                        <div class="col-md-12 text-center">
                                            <p class="text-danger">{{ __('messages.payWithCardText') }}</p>
                                            <h3 class="text-primary">
                                                {{ $input['amount'] }} {{ $input['currency'] }}
                                            </h3>
                                            <p>{{ $input['email'] }}</p>
                                        </div>
                                    </div>

                                    <form action="{{ route('test.hostedAPI.cardSubmit', $session_id) }}" method="POST"
                                        onsubmit='document.getElementById("disableBTN").disabled=true; document.getElementById("disableBTN")'>
                                        @csrf
                                        <input type="hidden" name="card_type" id='card_type'>
                                        <div class="pd-25">
                                            <div class="form-group">
                                                <label>{{ __('messages.cardNo') }}</label>
                                                <div id="creditCardType" class="d-flex tx-28 tx-gray-500 mg-t-10"
                                                    style="float: right;">
                                                    <div class="visa lh-1 mg-l-5"
                                                        style="font-size: 24px; margin-right: 5px;">
                                                        <i class="fa fa-cc-visa"></i>
                                                    </div>
                                                    <div class="mastercard lh-1 mg-l-5"
                                                        style="font-size: 24px; margin-right: 5px;">
                                                        <i class="fa fa-cc-mastercard"></i>
                                                    </div>
                                                    <div class="jcb lh-1 mg-l-5"
                                                        style="font-size: 24px; margin-right: 5px;">
                                                        <i class="fa fa-cc-jcb"></i>
                                                    </div>
                                                </div>
                                                <div class="input-group">
                                                    <input type="text" name="card_no" value=""
                                                        placeholder="Card No."
                                                        class="form-control inputCreditCard fld-txt" id="card"
                                                        required>
                                                </div>
                                                <strong class="text-danger log"></strong>
                                            </div>
                                            <div class="row mt-1">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>{{ __('messages.cardExpiry') }}</label>
                                                        <input class="expiration-month-and-year form-control fld-txt"
                                                            type="text" name="ccExpiryMonthYear"
                                                            placeholder="MM / YY" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>{{ __('messages.cvvNo') }}</label>
                                                        <small class="sm-right" data-toggle="tooltip"
                                                            data-placement="top"
                                                            title="{{ __('messages.cvvDesc') }}">{{ __('messages.whatIsThis') }}</small>
                                                        <input type="text" name="cvvNumber" value=""
                                                            placeholder="{{ __('messages.cvvNo') }}"
                                                            class="form-control fld-txt" id="cvvNumber" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="common-btns fl-btns mt-2">
                                                <button type="submit" class="btn btn-danger"
                                                    id="disableBTN">{{ __('messages.payNow') }}
                                                    {{ $input['currency'] }} {{ $input['amount'] }}</button>
                                                <a href="{{ route('iframe-checkout-cancel', $session_id) }}"
                                                    class="btn btn-primary cancel-btn">{{ __('messages.cancel') }}</a>
                                            </div>
                                        </div>

                                        <div class="card-type"></div>
                                    </form>
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

    <script src="{{ storage_asset('NewTheme/assets/lib/cleave.js/cleave.min.js') }}"></script>
    <script src="{{ storage_asset('NewTheme/js/creditly.js') }}"></script>
    <script src="{{ storage_asset('NewTheme/js/jquery.validity.js') }}"></script>

    <script>
        jQuery(document).ready(function() {
            jQuery("#load").fadeOut();
            jQuery("#loading").delay().fadeOut("");
        });
    </script>

    <script>
        $(function() {
            Creditly.initialize(
                '.expiration-month-and-year',
                '.card-type');
        });
        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });
        var cleave = new Cleave('.inputCreditCard', {
            creditCard: true,
            onCreditCardTypeChanged: function(type) {
                console.log(type)
                var card = $('#creditCardType').find('.' + type);
                $("#card_type").val(type);
                if (card.length) {
                    card.addClass('text-primary');
                    card.siblings().removeClass('text-primary');
                } else {
                    $('#creditCardType span').removeClass('text-primary');
                }
            }
        });
    </script>
</body>

</html>
