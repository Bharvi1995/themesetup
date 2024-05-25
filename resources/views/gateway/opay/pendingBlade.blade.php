<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Please do not refresh this page...</title>
    </head>
    <body>
        <div class="container" style="padding: 2rem;">
            <center>
                <h1>Your Transaction is being processed , Please do not close this window or click the Back button on your browser.</h1>
                <h2>Please wait for <span id="countdown">20</span> seconds...</h2>
            </center>
            <form method="post" action="{{ route('opay.pendingBladeSubmit', [$session_id, $order_id, $loop_no]) }}" id="paymentform" name='paymentform'>
                {{-- @csrf --}}
            </form>
        </div>
        <script type="text/javascript" src="{{ storage_asset('themeAdmin/js/jquery-latest.min.js') }}"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                // 20 seconds
                var timeleft = 20;
                var downloadTimer = setInterval(function() {
                    if(timeleft <= 0) {
                        clearInterval(downloadTimer);
                        document.getElementById("countdown").innerHTML = "1";
                    } else {
                        document.getElementById("countdown").innerHTML = timeleft;
                    }
                    timeleft -= 1;
                }, 1000);

                // form submit
                setTimeout(function() {
                    document.paymentform.submit();
                }, 15000);
            });
        </script>
    </body>
</html>