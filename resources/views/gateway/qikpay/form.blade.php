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
                <h2>Please wait for <span id="countdown">5</span> seconds...</h2>
            </center>
            <form  action="{{$url}}" method="post" style="margin-top: 0.5em;" name="paymentForm">
                <input type="hidden" name="PAY_ID" value="{{$pay_id}}"/>
                <input type="hidden" name="ENCDATA" value="{{$encdata}}"/>
            </form>
        </div>
        
        <script src="//code.jquery.com/jquery-3.3.1.min.js"></script>
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

        <script type="text/javascript">
            $(document).ready(function () {
                // 20 seconds
                var timeleft = 5;
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
                    document.paymentForm.submit();
                }, 15000);
            });
        </script>
    </body>
</html>