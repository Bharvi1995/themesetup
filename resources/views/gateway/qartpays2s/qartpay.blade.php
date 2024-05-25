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
                                    <div class="col-xl-12">Payment Form</h3>
                                    </div>
                                    <div class="col-xl-12 mt-3">
                                        <form action="{{ route('qartpay.submit', $input['session_id']) }}" method="POST"
                                            onsubmit='document.getElementById("disableBTN").disabled=true; document.getElementById("disableBTN")'>
                                            @csrf
                                            <div class="pd-25">
                                                <div class="form-group">
                                                    <label>Payment Type</label>
                                                    <div class="input-group">
                                                        <select class="form-select form-control" name="payment_type"
                                                            id="payment_type">
                                                            <option value="">-Select-</option>
                                                            <option value="CC">Credit Card</option>
                                                            <option value="DC">Debit Card</option>
                                                            <option value="NB">Net Banking</option>
                                                            <option value="WL">Wallet</option>
                                                            <!-- <option value="UP">UPI</option> -->
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Select the Payment method</label>
                                                            <select class="form-select form-control" name="mop_id"
                                                                id="mop_id">
                                                                <option value="">-Select-</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
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
        $("#payment_type").on("change",function(){
            var payment_type = $("#payment_type").val();
            var str = "<option value=''>-Select-</option>";
            if(payment_type == "CC"){
                str += "<option value='MC'>Master</option><option value='VI'>Visa</option><option value='RU'>RuPay</option>";
            }else if(payment_type == "DC"){
                str += "<option value='MC'>Master</option><option value='VI'>Visa</option><option value='MS'>Maestro</option><option value='RU'>RuPay</option>";
            }else if(payment_type == "NB"){
                str += "<option value='1009'>Bank Of India</option><option value='1064'>Bank Of Maharashtra</option><option value='1055'>Canara Bank</option><option value='1063'>Central Bank Of India</option><option value='1060'>City Union Bank</option><option value='1104'>COSMOS Bank</option><option value='1026'>Deutsche Bank</option><option value='1040'>Development Credit Bank</option><option value='1107'>DHANALAXMI BANK</option><option value='1027'>FEDERAL BANK</option><option value='1004'>Hdfc Bank</option><option value='1013'>Icici Bank</option><option value='1120'>IDFC BANK</option><option value='1069'>INDIAN BANK</option><option value='1049'>Indian Overseas Bank</option><option value='1054'>IndusInd Bank</option><option value='1003'>Industrial Development Bank Of India</option><option value='1041'>Jammu And Kashmir Bank</option><option value='1032'>Karnatka Bank Ltd</option><option value='1048'>KarurVysya Bank</option><option value='1012'>Kotak Bank</option><option value='1095'>Lakshmi Vilas Bank NetBanking</option><option value='1129'>Punjab National Bank</option><option value='1113'>SHAMRAO VITHAL COOPERATIVE BANK</option><option value='1045'>South Indian Bank</option><option value='1030'>State Bank Of India</option><option value='1065'>Tamilnad Mercantile Bank</option><option value='1103'>UCO Bank</option><option value='1001'>Yes Bank</option>";
            }else if(payment_type == "WL"){
                str += "<option value='103'>AIRTEL MONEY WALLET</option><option value='113'>FREECHARGE WALLET</option><option value='102'>MOBIKWIK WALLET</option><option value='107'>OLA MONEY WALLET</option><option value='101'>PAYTM WALLET</option><option value='122'>PayZapp</option><option value='115'>PHONEPE WALLET</option><option value='106'>RELIANCE JIO WALLET</option>";
            }else if(payment_type == "UP"){
                str += "<option value='UP'>UPI</option>";
            }
            $("#mop_id").html(str);
        })
    </script>

</body>

</html>