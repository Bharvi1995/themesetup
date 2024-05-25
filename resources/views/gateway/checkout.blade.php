<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ config('app.name') }} | Checkout Form</title>
    <!-- Favicon icon -->
    <link rel="shortcut icon" href="{{ storage_asset('NewTheme/images/favicon.ico') }}" />
    <link href="{{ storage_asset('theme/css/style.css') }}" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&family=Roboto:wght@100;300;400;500;700;900&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css">

    <link rel="stylesheet" href="{{ storage_asset('/theme/vendor/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ storage_asset('themeAdmin/css/typography.css') }}">
    <link href="{{ storage_asset('themeAdmin/css/custom.css') }}" rel="stylesheet">

    <style type="text/css">
        .grecaptcha-badge {
            z-index: 1000;
        }

        .authincation-content {
            background-color: #2B2B2B;
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

        .form-group label {
            color: #000;
        }

        .form-control {
            border-radius: 0px;
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
    </style>
</head>

<body class="h-100">
    <div class="authincation h-100">
        <div class="container h-100">
            <div class="row justify-content-center h-100vh align-items-center">
                <div class="col-md-6">
                    <div class="text-center mb-3">
                        <a href="{{ route('login') }}">
                            <img src="{{ storage_asset('NewTheme/images/Logo.png') }}" alt="" width="300px">
                        </a>
                    </div>
                    <div class="authincation-content">
                        <div class="row no-gutters">
                            <div class="col-xl-12">
                                <div class="auth-form row">
                                    <div class="col-xl-12">
                                        <h3 class="text-center mb-5">Pay with your card</h3>
                                    </div>
                                    <div class="col-xl-12 text-center">
                                        <h3 class="text-info">
                                            <strong>150</strong> <small>USD</small>
                                        </h3>
                                        <h6 class="text-info">example@gmail.com</h6>
                                    </div>
                                    <div class="col-xl-12 mt-3">
                                        <form action="#" method="POST"
                                            onsubmit='document.getElementById("disableBTN").disabled=true; document.getElementById("disableBTN")'>
                                            <div class="pd-25">
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
                                                        <div class="jcb lh-1 mg-l-5"><i class="fab fa-cc-jcb"></i></div>
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
                                                <div class="common-btns fl-btns">
                                                    <button type="submit" class="black-btn btn btn-block"
                                                        id="disableBTN">Pay USD 150</button>
                                                    <a href="#" class="btn btn-block cancel-btn">Cancel</a>
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