<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ config('app.name') }} | Checkout Form</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ storage_asset('NewTheme/images/favicon.ico') }}">
    <link href="{{ storage_asset('theme/css/style.css') }}" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&family=Roboto:wght@100;300;400;500;700;900&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css">

    <link rel="stylesheet" href="{{ storage_asset('/theme/vendor/select2/css/select2.min.css') }}">

    <style type="text/css">
        .grecaptcha-badge {
            z-index: 1000;
        }

        .authincation-content {
            background-color: #fff;
        }

        .h-100vh {
            min-height: 100vh !important;
        }

        .auth-form .text-danger {
            color: #842e2e !important;
        }

        .form-control:hover,
        .form-control:focus,
        .form-control.active {
            border-color: #E89C86;
        }

        .form-control:hover,
        .form-control:focus,
        .form-control.active,
        .select2-container--default .select2-selection--single .select2-selection__rendered,
        .form-control {
            color: #5a5a5a;
            font-size: 0.875rem;
        }

        label.error-block {
            color: red !important;
        }

        input.error,
        textarea.error,
        select.error {
            border-color: red !important;
        }

        input.mismatch,
        textarea.mismatch,
        select.mismatch {
            border-color: orange !important;
        }

        input.valid,
        textarea.valid,
        select.valid {
            border-color: green !important;
        }

        label.error {
            color: red !important;
        }

        label.mismatch {
            border-color: orange !important;
        }

        label.valid {
            color: green !important;
        }

        .black-btn {
            background-color: #000;
            color: #fff;
        }

        .cancel-btn {
            border-color: #5a5a5a;
        }

        #creditCardType i {
            font-size: 26px;
            margin: 5px 0px 0px 5px;
        }

        .form-control {
            border-radius: 0px;
        }

        .form-group label {
            color: #000;
        }
    </style>
</head>

<body class="h-100">
    <div class="authincation h-100">
        <div class="container h-100">
            <div class="row justify-content-center h-100vh align-items-center">
                <div class="col-md-6">
                    <div class="text-center mb-3">
                        <a href="{{ route('login') }}">
                            @if($userData->iframe_logo != '' && $userData != NULL)
                            <img src="{{ getS3Url($userData->iframe_logo) }}" style="max-width: 250px;">
                            @else
                            <img src="{{ storage_asset('NewTheme/images/Logo.png') }}" width="300px">
                            @endif
                        </a>
                    </div>
                    <div class="authincation-content">
                        <div class="row no-gutters">
                            <div class="col-xl-12">
                                <div class="auth-form row">
                                    <div class="col-xl-12">
                                        <h3 class="text-center mb-5">Pay with {{ ($request->payment_type == "CC" ||
                                            $request->payment_type == "DC")?" your card" : "your UPI" }}</h3>
                                    </div>
                                    <div class="col-xl-12 text-center">
                                        <h3 class="text-info">
                                            <strong>{{ $input['amount'] }}</strong> <small>{{ $input['currency']
                                                }}</small>
                                        </h3>
                                        <h6 class="text-info">{{ $input['email'] }}</h6>
                                    </div>
                                    <div class="col-xl-12 mt-3">
                                        <form action="{{ route('qikpays2s.formSendData', $input['session_id']) }}"
                                            method="POST"
                                            onsubmit='document.getElementById("disableBTN").disabled=true; document.getElementById("disableBTN")'>
                                            @csrf
                                            <input type="hidden" name="payment_type" id='payment_type'
                                                value="{{$request->payment_type}}">
                                            <input type="hidden" name="mop_id" id='mop_id' value="{{$request->mop_id}}">
                                            <div class="pd-25">
                                                @if($request->payment_type == "CC" || $request->payment_type == "DC")
                                                <div class="form-group">
                                                    <label>Card No.</label>
                                                    <div class="input-group">
                                                        <input type="text" name="card_no" value=""
                                                            placeholder="Card No."
                                                            class="form-control inputCreditCard fld-txt" id="card"
                                                            required>
                                                    </div>
                                                    <div id="creditCardType" class="d-flex tx-28 tx-gray-500 mg-t-10">
                                                        <div class="visa lh-1 mg-l-5"><i class="fab fa-cc-visa"></i>
                                                        </div>
                                                        <div class="mastercard lh-1 mg-l-5"><i
                                                                class="fab fa-cc-mastercard"></i></div>
                                                        {{-- <div class="jcb lh-1 mg-l-5"><i class="fab fa-cc-jcb"></i>
                                                        </div> --}}
                                                    </div>
                                                    <strong class="text-danger log"></strong>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Expiry Month/Year</label>
                                                            <input
                                                                class="expiration-month-and-year form-control fld-txt"
                                                                type="text" name="ccExpiryMonthYear"
                                                                placeholder="MM / YY" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>CVV No.</label>
                                                            <small class="sm-right" data-toggle="tooltip"
                                                                data-placement="top"
                                                                title="This CVV is a 3 digit security code located at the back of your card">What
                                                                is this?</small>
                                                            <input type="text" name="cvvNumber" value=""
                                                                placeholder="CVV No." class="form-control fld-txt"
                                                                id="cvvNumber" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                @elseif($request->payment_type == "UP")
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>UPI</label>
                                                            <input class="form-control fld-txt" type="text"
                                                                name="txtUPI" placeholder="UPI" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                                <div class="common-btns fl-btns">
                                                    <button type="submit" class="black-btn btn btn-block"
                                                        id="disableBTN">Pay {{ $input['currency'] }} {{ $input['amount']
                                                        }}</button>
                                                    <a href="{{route('iframe-checkout-cancel',$input['session_id'])}}"
                                                        class="btn btn-block cancel-btn">Cancel</a>
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
    </div>
    <br>


    <script src="{{ storage_asset('NewTheme/assets/lib/cleave.js/cleave.min.js') }}"></script>

    <script src="{{ storage_asset('theme/vendor/global/global.min.js') }}"></script>

    <script src="{{ storage_asset('theme/js/creditly.js') }}"></script>
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
            onCreditCardTypeChanged: function (type) {
                console.log(type)
                var card = $('#creditCardType').find('.'+type);
                $("#card_type").val(type);
                if(card.length) {
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