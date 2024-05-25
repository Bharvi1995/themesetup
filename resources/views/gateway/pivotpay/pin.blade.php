<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Enter your OTP</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ storage_asset('NewTheme/images/favicon.ico') }}">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('NewTheme/css/bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('NewTheme/css/custom.css') }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"
        integrity="sha512-CNgIRecGo7nphbeZ04Sc13ka07paqdeTu0WR1IM4kNcpmBAUSHSQX0FslNhTDadL4O5SAGapGt4FodqL8My0mA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <style>
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
        <div class="formDiv p-2 shadow-lg ">
            <p class="text-danger ">Please enter the PIN/OTP which your received on your phone/email for
                <strong>{{ $amount }}
                    {{ $currency }}</strong>
            </p>
            <form method="POST" action="{{ route('store.pivot.pin') }}">
                @csrf
                <div class="mb-2">
                    <input type="hidden" name="id" value="{{ $id }}" />
                    <input type="text" class="form-control" placeholder="Enter your pin" name="otp" />
                    @error('otp')
                        <span class="text-danger ">{{ $message }}</span>
                    @enderror
                </div>
                <div class="d-grid ">
                    <button class="btn btn-primary ">Submit</button>
                </div>
            </form>
        </div>
    </div>



    <script type="text/javascript" src="{{ storage_asset('themeAdmin/js/jquery-latest.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $("#loadingDiv").hide();
        });
    </script>
</body>

</html>
