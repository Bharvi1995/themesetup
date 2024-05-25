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
            <form  action="https://dashboard.qartpay.com/crm/jsp/merchantpay" method="post" style="margin-top: 0.5em;" name="paymentForm">
                <input type="hidden" name="AMOUNT" value="{{$data['AMOUNT']}}"/>
                <input type="hidden" name="CURRENCY_CODE" value="356"/>
                <input type="hidden" name="CUST_EMAIL" value="{{$data['CUST_EMAIL']}}"/>
                <input type="hidden" name="CUST_NAME" value="{{$data['CUST_NAME']}}"/>
                <input type="hidden" name="CUST_PHONE" value="{{$data['CUST_PHONE']}}"/>
                <input type="hidden" name="MOP_TYPE" value="{{$data['MOP_TYPE']}}"/>
                <input type="hidden" name="ORDER_ID" value="{{$data['ORDER_ID']}}"/>
                <input type="hidden" name="PAY_ID" value="{{$data['PAY_ID']}}"/>
                <input type="hidden" name="PAYMENT_TYPE" value="{{$data['PAYMENT_TYPE']}}"/>
                <input type="hidden" name="PRODUCT_DESC" value="{{$data['PRODUCT_DESC']}}"/>
                <input type="hidden" name="RETURN_URL" value="{{$data['RETURN_URL']}}"/>
                <input type="hidden" name="TXNTYPE" value="{{$data['TXNTYPE']}}"/>
                <?php
                if($data['PAYMENT_TYPE'] == "CC" || $data['PAYMENT_TYPE'] == "DC"){
                    ?>
                    <input type="hidden" name="CARD_NUMBER" value="{{$data['CARD_NUMBER']}}"/>
                    <input type="hidden" name="CARD_EXP_DT" value="{{$data['CARD_EXP_DT']}}"/>
                    <input type="hidden" name="CVV" value="{{$data['CVV']}}"/>
                    <?php
                }elseif($data['PAYMENT_TYPE'] == "UP"){
                    ?>
                    <input type="hidden" name="UPI" value="{{$data['UPI']}}"/>
                    <?php
                }
                ?>
                <input type="hidden" name="HASH" value="{{$data['HASH']}}"/>
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