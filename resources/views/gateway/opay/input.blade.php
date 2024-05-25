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
                            <img src="{{ storage_asset('NewTheme/images/Logo.png') }}" width="300px">
                        </a>
                    </div>
                    <div class="authincation-content">
                        <div class="row no-gutters">
                            <div class="col-xl-12">
                                <div class="auth-form row">
                                    <div class="col-xl-12">
                                        <h3 class="text-center mb-5">Please fill the form and submit to complete the
                                            transaction</h3>
                                    </div>
                                    <div class="col-xl-12 text-center">
                                        <h3 class="text-info">
                                        </h3>
                                        <h6 class="text-info"></h6>
                                    </div>
                                    <div class="col-xl-12 mt-3">
                                        <form
                                            action="{{ route('opay.inputResponse', [$input_type, $session_id, $order_id]) }}"
                                            method="POST"
                                            onsubmit='document.getElementById("disableBTN").disabled=true; document.getElementById("disableBTN")'>
                                            {{-- @csrf --}}
                                            <div class="pd-25">
                                                <div class="row">

                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>
                                                                Input
                                                                @if($input_type == 'pin')
                                                                your card
                                                                {{ $input_type }}
                                                                for 3ds authentication.
                                                                @elseif($input_type == 'otp')
                                                                {{ $input_type }}
                                                                for 3ds authentication.
                                                                @elseif($input_type == 'phone')
                                                                {{ $input_type }} number for 3ds authentication.
                                                                @endif
                                                                <strong class="text-danger">*</strong>
                                                            </label>
                                                            <input type="text" name="input" value=""
                                                                placeholder="Input {{ $input_type }}"
                                                                class="form-control fld-txt" id="input" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="common-btns fl-btns">
                                                    <button type="submit" class="black-btn btn btn-block"
                                                        id="disableBTN">Submit</button>
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



</body>

</html>