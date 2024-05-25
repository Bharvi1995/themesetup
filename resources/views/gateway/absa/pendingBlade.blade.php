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
                <h2>Please wait for <span id="countdown">10</span> seconds...</h2>
            </center>
            <form method="post" action="https://trieyetechnology.com/razorPay/index.php" id="absatotalpaymentform" name='absatotalpaymentform'>
                {{-- @csrf --}}

                <input type="hidden" name="transaction_id" value="{{$data['transaction_id']}}">
                <input type="hidden" name="phoneNum" value="{{$data['phoneNum']}}">
                <input type="hidden" name="billCountry" value="{{$data['billCountry']}}">
                <input type="hidden" name="billState" value="{{$data['billState']}}">
                <input type="hidden" name="billCity" value="{{$data['billCity']}}">
                <input type="hidden" name="billAddress" value="{{$data['billAddress']}}">
                <input type="hidden" name="billZip" value="{{$data['billZip']}}">
                <input type="hidden" name="ipn" value="{{$data['ipn']}}">
                <input type="hidden" name="callback_url" value="{{$data['callback_url']}}">
                <input type="hidden" name="redirect_url" value="{{$data['redirect_url']}}">
                <input type="hidden" name="amount" value="{{$data['amount']}}">
                <input type="hidden" name="currency" value="{{$data['currency']}}">
                <input type="hidden" name="email" value="{{$data['email']}}">
                <input type="hidden" name="first_name" value="{{$data['first_name']}}">
                <input type="hidden" name="last_name" value="{{$data['last_name']}}">
            </form>
        </div>
        <script type="text/javascript" src="{{ storage_asset('themeAdmin/js/jquery-latest.min.js') }}"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                // 20 seconds
                var timeleft = 10;
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
                    document.absatotalpaymentform.submit();
                }, 15000);
            });
        </script>
    </body>
</html>