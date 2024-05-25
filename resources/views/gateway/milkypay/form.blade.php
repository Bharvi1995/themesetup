<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Please do not refresh the page</title>

    <style>
        body {
            /*background-color: #262626 !important;
            co*/lor: #B3ADAD !important;
        }

       /* #loadingDiv {
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
        }*/
    </style>
</head>

<body>
    <div id="loadingDiv">
        <span class="loader"></span>
        <p>Loading ...</p>
        <strong>Please do not refresh the page</strong>
    </div>
    <form method="{{ $method }}" action="{{ $action }}" id="paymentForm" name="paymentForm">
        @foreach ($fields as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endforeach
    </form>

    <script type="text/javascript" src="{{ storage_asset('themeAdmin/js/jquery-latest.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            setTimeout(() => {
                document.paymentForm.submit();
            }, 1000);

        });
    </script>
</body>

</html>
