<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" integrity="sha512-3pIirOrwegjM6erE5gPSwkUzO+3cTjpnV9lexlNZqvupR64iZBnOOTiiLPb9M36zpMScbmUNIcHUqKD47M719g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .container-fluid .panel {
            position: absolute;
            top: 30%;
            left: 50%;
            transform: translateX(-50%) translateY(-50%);
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="panel panel-default col-md-4">
            <div class="panel-body text-center">
                <h3>Please send a total of {{ $paymentResponse->data->payment_amount }} {{ $paymentResponse->data->payment_currency }} to the address mentioned</h1>
                    <br />
                    <h4><b>Address:</b> {{ $paymentResponse->data->payment_address }}
                </h3>
                <h4><b>Amount:</b> {{ $paymentResponse->data->payment_amount }}</h3>
                    <div class="padding-15px-bottom">
                        <img class="qr-code img-thumbnail img-responsive" src="{{$paymentResponse->data->qrCode}}" />
                    </div>
                    <h2>Please wait for <span id="countdown">180</span> seconds...</h2>
                    <div class="form-group col-md-12 mt-3">
                        <a href="{{route('basqet.back',$sessionId)}}" class="btn btn-danger btn-sm">Back to the store</a>
                    </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script type="text/javascript">
        var transaction_id = '<?php echo $gateway_id; ?>';
        var secret_key = '<?php echo $secret_key; ?>';
        setInterval(checkTransactionStatus, 60000);
        var timeleft = 180;
        var downloadTimer = setInterval(function() {
            if (timeleft <= 0) {
                clearInterval(downloadTimer);
                document.getElementById("countdown").innerHTML = "1";
                window.location.href = '<?php echo route("basqet.pending", $sessionId); ?>'
            } else {
                document.getElementById("countdown").innerHTML = timeleft;
            }
            timeleft -= 1;
        }, 1000);

        function checkTransactionStatus() {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: "{{ URL::route('basqet.verify') }}",
                data: {
                    'transaction_id': transaction_id,
                    'secret_key': secret_key,
                    'session_id': "{{ $sessionId}}"
                },
                success: function(data) {
                    if (data.status == 1 || data.status == 0) {
                        window.location.href = data.url;
                    }
                },
            });
        }
    </script>
</body>

</html>
