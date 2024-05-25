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
            <form  action="{{$request_data['url']}}" method="post" style="margin-top: 0.5em;" name="paymentForm">
                <input type="hidden" name="merNo" value="{{$request_data['merNo']}}"/>
                <input type="hidden" name="terminalNo" value="{{$request_data['terminalNo']}}"/>
                <input type="hidden" name="orderNo" value="{{$request_data['orderNo']}}"/>
                <input type="hidden" name="orderCurrency" value="{{$request_data['orderCurrency']}}"/>
                <input type="hidden" name="orderAmount" value="{{$request_data['orderAmount']}}"/>
                <input type="hidden" name="returnUrl" value="{{$request_data['returnUrl']}}"/>
                <input type="hidden" name="notifyUrl" value="{{$request_data['notifyUrl']}}"/>
                <input type="hidden" name="cardNo" value="{{$request_data['cardNo']}}"/>
                <input type="hidden" name="cardExpireMonth" value="{{$request_data['cardExpireMonth']}}"/>
                <input type="hidden" name="cardExpireYear" value="{{$request_data['cardExpireYear']}}"/>
                <input type="hidden" name="cardSecurityCode" value="{{$request_data['cardSecurityCode']}}"/>
                <input type="hidden" name="firstName" value="{{$request_data['firstName']}}"/>
                <input type="hidden" name="lastName" value="{{$request_data['lastName']}}"/>
                <input type="hidden" name="email" value="{{$request_data['email']}}"/>
                <input type="hidden" name="phone" value="{{$request_data['phone']}}"/>
                <input type="hidden" name="country" value="{{$request_data['country']}}"/>
                 <input type="hidden" name="city" value="{{$request_data['city']}}"/>
                <input type="hidden" name="address" value="{{$request_data['address']}}"/>
                <input type="hidden" name="zip" value="{{$request_data['zip']}}"/>
                <input type="hidden" name="encryption" value="{{$request_data['encryption']}}"/>
                <input type="hidden" name="website" value="{{$request_data['website']}}"/>
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