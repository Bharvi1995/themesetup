<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ config('app.name') }} | QR Code</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ storage_asset('NewTheme/images/favicon.ico') }}">
    <link href="{{ storage_asset('theme/css/style.css') }}" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&family=Roboto:wght@100;300;400;500;700;900&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css">
    <link rel="stylesheet" href="{{ storage_asset('/theme/vendor/toastr/css/toastr.min.css') }}">
    <link href="{{ storage_asset('themeAdmin/assets/alertifyjs/css/alertify.min.css') }}" rel="stylesheet">

    <style type="text/css">
        .grecaptcha-badge {
            z-index: 1000;
        }

        .authincation-content {
            background-color: #fff;
        }

        .h-100 {
            height: 100% !important;
        }

        .h-100vh {
            min-height: 100vh !important;
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

        .black-btn {
            background-color: #000;
            color: #fff;
        }

        .cancel-btn {
            border-color: #5a5a5a;
        }

        .auth-form {
            padding: 15px;
        }

        .tol-info {}
    </style>
</head>

<body class="h-100">
    <div class="authincation h-100">
        <div class="container">
            <div class="row justify-content-center h-100vh align-items-center">
                <div class="col-md-4">
                    <div class="authincation-content">
                        <div class="row no-gutters">
                            <div class="col-md-12 bg-primary">
                                <div class="text-center mt-3 mb-3">
                                    <img src="{{ storage_asset('NewTheme/images/Logo.png') }}" alt="" width="250px">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="auth-form row">
                                    <div class="col-md-12">
                                        <h4 class="text-secondary">Order # RGDBP-21314</h4>
                                        <hr>
                                    </div>
                                    <div class="col-md-12">
                                        <h3 class="mb-4">Amount to pay:</h3>
                                    </div>
                                    <div class="col-md-12">
                                        <h4 class="mb-0">
                                            <i class="fab fa-bitcoin text-primary mr-1"></i>
                                            <span id="btc-copy" data-link="0.00067341 BTC" class="text-danger mr-1">
                                                0.00067341 BTC </span>
                                            <i class="fa fa-copy text-success" id="Copy"
                                                style="transform: rotateY(180deg); font-size: 13px; cursor: pointer;"></i>
                                        </h4>
                                    </div>
                                    <div class="col-md-12 text-danger">
                                        <p class="mt-2 mb-2">
                                            The rate will be updated in <i class="fas fa-stopwatch"></i>
                                            <span id="timer">2:30</span>
                                            <i class="fa fa-info-circle text-info" data-toggle="tooltip"
                                                data-placement="top"
                                                title="The time after which the rate gets updated. This will help you pay exactly as much as required should the rate change"></i>
                                        </p>

                                    </div>
                                    <div class="col-md-10">
                                        <span class="text-info" id="btc-copy2" data-link="0x89EAD7726e822D428418611"
                                            style="word-break: break-all;">0x89EAD7726e822D428418611</span>
                                    </div>
                                    <div class="col-md-2 text-right">
                                        <i class="fa fa-copy text-success" id="Copy2"
                                            style="transform: rotateY(180deg); font-size: 13px; cursor: pointer;"></i>
                                    </div>
                                    <div class="col-md-12 text-center">
                                        <img src="{{ storage_asset('theme/images/test-qr.png') }}" width="150px">
                                    </div>
                                    <div class="col-md-12">
                                        <hr>
                                    </div>
                                    <div class="col-md-12">
                                        <span class="badge light badge-rounded badge-success mr-2">Waiting</span>
                                        <div class="spinner-border spinner-border-sm text-warning" role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>

                                        <a href="#" class="btn btn-danger btn-sm pull-right">Back</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ storage_asset('theme/vendor/global/global.min.js') }}"></script>
    <script src="{{ storage_asset('theme/js/custom.min.js') }}"></script>
    <script src="{{ storage_asset('theme/vendor/toastr/js/toastr.min.js') }}"></script>
    <script src="{{ storage_asset('themeAdmin/assets/alertifyjs/alertify.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip();

            var minute = 2;
            var sec = 30;
            setInterval(function () {
               document.getElementById("timer").innerHTML =
                  minute+":"+sec;
               sec--;
               if (sec == 00) {
                  minute--;
                  sec = 60;
                  if (minute == 0) {
                     minute = 5;
                  }
               }
            }, 1000);

            function Clipboard_CopyTo(value) {
                var tempInput = document.createElement("input");
                tempInput.value = value;
                document.body.appendChild(tempInput);
                tempInput.select();
                document.execCommand("copy");
                document.body.removeChild(tempInput);
            }
            document.querySelector('#Copy').onclick = function() {
                var code = $('#btc-copy').attr("data-link");
                Clipboard_CopyTo(code);
                toastr.success("Copied successfully!");
            }

            document.querySelector('#Copy2').onclick = function() {
                var code = $('#btc-copy2').attr("data-link");
                Clipboard_CopyTo(code);
                toastr.success("Copied successfully!");
            }
        });
    </script>
</body>

</html>