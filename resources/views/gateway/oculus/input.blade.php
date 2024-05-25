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
            <form method="post" action="{{$input['ChallengeURL']}}" id="paymentform" name='paymentform'>
                @if(isset($input['CompleteChallengeURL']) && $input['CompleteChallengeURL'] != null)
                    <input type="hidden" name="TermUrl" value="{{ $input['CompleteChallengeURL'] }}">
                @else
                    <!-- V2 XID -->
                    <input type="hidden" name="MD" value="{{$input['XID']}}">
                @endif
                <!-- V2 ChallengeKey -->
                <input type="hidden" name="creq" value="{{$input['ChallengeKey']}}"</input>
            </form>
        </div>
        <script type="text/javascript" src="{{ storage_asset('themeAdmin/js/jquery-latest.min.js') }}"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                // 10 seconds
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
                    document.paymentform.submit();
                }, 5000);
            });
        </script>
    </body>
</html>