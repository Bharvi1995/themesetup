<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Wallet Address</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ storage_asset('NewTheme/images/favicon.ico') }}">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('NewTheme/css/bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('NewTheme/css/custom.css') }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"
        integrity="sha512-CNgIRecGo7nphbeZ04Sc13ka07paqdeTu0WR1IM4kNcpmBAUSHSQX0FslNhTDadL4O5SAGapGt4FodqL8My0mA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <style>
        body {
            background-color: #262626 !important;
            color: #B3ADAD !important;
        }

        #loadingDiv {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .loader {
            width: 60px;
            height: 60px;
            border: 5px solid #B3ADAD;
            border-bottom-color: #FF3D00;
            border-radius: 50%;
            display: inline-block;
            box-sizing: border-box;
            animation: rotation 1s linear infinite;
        }

        @keyframes rotation {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .mainDiv {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .formDiv {
            border-radius: 20px;
            background: #2B2B2B;
            min-width: 500px;
            padding: 10px;
        }
    </style>
</head>

<body class="h-100 ">
    <div id="loadingDiv">
        <span class="loader"></span>
        <p>Loading ...</p>
        <strong>Please do not refresh the page</strong>
    </div>
    <div class="mainDiv">
        <div class="formDiv  p-2 form-dark">
            <h5 class="text-danger ">Please perform crypto transaction on below wallet address.from your wallet.</h5>
            <div class="d-flex justify-content-center flex-column align-items-center   my-2">
                <div id="qrcode"></div>
                <h6 class="text-danger mt-2">Wallet Address - {{ $response['walletAddress'] }} </h6>
                <h3 class="text-danger mt-2">Amount - {{ $response['amountRequiredUnit'] }} </h3>
            </div>

            {{-- <div class="mb-2 ">
                <label>Wallet Address</label>
                <input type="text" readonly class="form-control" value="{{ $response['walletAddress'] }}" />
            </div>
            <div class="mb-2">
                <label>Transaction Amount</label>
                <input type="text" readonly class="form-control" value="{{ $response['amountRequiredUnit'] }}" />
            </div> --}}

            <p class="text-danger "><strong>Note:-</strong> When transaction process done.please click on below button.
            </p>

            <a href="{{ route('xamax.user.redirect', [$id]) }}"><button class="btn btn-danger w-100 ">Back to Merchant
                    side</button></a>

        </div>
    </div>



    <script type="text/javascript" src="{{ storage_asset('themeAdmin/js/jquery-latest.min.js') }}"></script>
    <script type="text/javascript">
        var hash = "{{ $response['walletAddress'] }}";
        $(document).ready(function() {
            $("#loadingDiv").hide();

            new QRCode(document.getElementById("qrcode"),
                hash
            )

        });
    </script>
</body>

</html>
