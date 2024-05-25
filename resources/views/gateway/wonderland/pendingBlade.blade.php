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
            <form method="post" action="https://pay.wonderlandpay.com/InDirectInterface" id="paymentform" name='paymentform'>
                {{-- @csrf --}}
                <input type="hidden" name="merNo" value="{{$check_assign_mid->mid_number}}" />
                <input type="hidden" name="gatewayNo" value="{{$check_assign_mid->gateway_no}}" />
                <input type="hidden" name="orderNo" value="{{$input['order_id']}}" />
                <input type="hidden" name="orderCurrency" value="{{$input['converted_currency']}}" />
                <input type="hidden" name="orderAmount" value="{{$input['converted_amount']}}" />
                <input type="hidden" name="returnUrl" value="{{route('wonderlandvisa.return',$input['session_id'])}}">
                <input type="hidden" name="notifyUrl" value="{{route('wonderlandvisa.notify',$input['session_id'])}}">
                <input type="hidden" name="cardNo" value="{{$cardDetails['0']}}" />
                <input type="hidden" name="cardExpireMonth" value="{{$input['ccExpiryMonth']}}" />
                <input type="hidden" name="cardExpireYear" value="{{$input['ccExpiryYear']}}" />
                
                <input type="hidden" name="cardSecurityCode" value="{{$cardDetails['1']}}" />
                <input type="hidden" name="firstName" value="{{$input['first_name']}}" />
                <input type="hidden" name="lastName" value="{{$input['last_name']}}" />
                <input type="hidden" name="email" value="{{$input['email']}}" />
                <input type="hidden" name="ip" value="{{$input['ip_address']}}" />
                <input type="hidden" name="phone" value="{{$input['phone_no']}} " />
                <input type="hidden" name="country" value="{{$input['country']}}" />
                <input type="hidden" name="state" value="{{$input['state']}}" />
                <input type="hidden" name="city" value="{{$input['city']}}" />
                <input type="hidden" name="address" value="{{$input['address']}}" />
                <input type="hidden" name="zip" value="{{$input['zip']}}" />
                <input type="hidden" name="signInfo" value="{{$signInfo}}" />
                <input type="hidden" name="random" value="{{$input['session_id']}}" />
            </form>
        </div>
        <script type="text/javascript" src="{{ storage_asset('themeAdmin/js/jquery-latest.min.js') }}"></script>
        <script type="text/javascript">
            $().ready(function () {
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

                //form submit
                setTimeout(function() {
                    document.paymentform.submit();
                }, 15000);
            });
        </script>
    </body>
</html>