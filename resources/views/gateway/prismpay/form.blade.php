<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Please do not refresh this page...</title>
    </head>
    <body>
        <style>
            #challengeiframe{
                width: 100%;
                min-height: 500px;
            }
        </style>
        <div class="container" style="padding: 2rem;">
            <center>
                <h1>Your Transaction is being processed , Please do not close this window or click the Back button on your browser.</h1>
                <h2>Please wait for <span id="countdown">10</span> seconds...</h2>
            </center>
            <form  id="cartForm" name='cartForm' method="post" style="display: none;">
                @csrf
                <input type="text" name="cardNumber" data-threeds="pan" value="{{$input['reqest_datacardNo']}}"/>
                <input type="text" name="amount" data-threeds="amount" value="{{ $input['converted_amount'] }}"/>
                <input type="text" name="cardMonth" data-threeds="month" value="{{$input['user_ccexpiry_month']}}"/>
                <input type="text" name="cardYear" data-threeds="year" value="{{substr($input['user_ccexpiry_year'], -2)}}"/>
                <input type="text" data-threeds="shippingLine1" value="{{ $input['user_address'] }}"/>
                <input type="text" data-threeds="shippingLine2" value="{{ $input['user_address'] }}"/>
                <input type="text" data-threeds="shippingLine3" value="{{ $input['user_address'] }}"/>
                <input type="text" data-threeds="shippingPostCode" value="{{ $input['user_zip'] }}"/>
                <input type="text" data-threeds="shippingCity" value="{{ $input['user_city'] }}"/>
                <input type="text" data-threeds="shippingCountry" value="{{$input['request_countrycode']}}"/>
                <input type="text" data-threeds="billingLine1" value="{{ $input['user_address'] }}"/>
                <input type="text" data-threeds="billingLine2" value="{{ $input['user_address'] }}"/>
                <input type="text" data-threeds="billingLine3" value="{{ $input['user_address'] }}"/>
                <input type="text" data-threeds="billingPostCode" value="{{ $input['user_zip'] }}"/>
                <input type="text" data-threeds="billingCity" value="{{ $input['user_city'] }}"/>
                <input type="text" data-threeds="billingCountry" value="{{$input['request_countrycode']}}"/>
                <input type="text" data-threeds="addrMatch" value="Y"/>
                <input type="text" data-threeds="email" value="{{ $input['user_email'] }}"/>
                <input type="text" name="cardHolderName" data-threeds="cardHolderName" value="{{ $input['user_first_name'].' '.$input['user_last_name'] }}"/>
                <input type="text" data-threeds="mobileCC" value="{{$input['reqest_datacountrycode']}}"/>
                <input type="text" data-threeds="mobilePhoneNum" value="{{ $input['user_phone_no'] }}"/>
                <input type="text" data-threeds="homeCC" value="{{$input['reqest_datacountrycode']}}"/>
                <input type="text" data-threeds="homePhoneNum" value="{{ $input['user_phone_no'] }}"/>
                <input type="text" data-threeds="workCC" value="{{$input['reqest_datacountrycode']}}"/>
                <input type="text" data-threeds="workPhoneNum" value="{{ $input['user_phone_no'] }}"/>
                <input type="text" data-threeds="currency" value="840"/>
                <input type="text" name="idData3Ds" id="idData3Ds">
                <input type="text" name="sessionId" id="sessionId" value="{{ $input['session_id']}}">
                <input type="text" name="txtJwt" id="txtJwt" value="{{$authentication['jwt']}}">
                <input type="button" >
            </form>
        </div>
        <iframe id="challengeiframe" frameborder="0"></iframe>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
        <!-- <script src="https://cdn.prismpay.com/3ds/js/pp3dssdk-1.0.3.min.js"></script> -->
        <script type="text/javascript" src="{{$authentication['SdkUrl']}}"></script>
        <script>
            PP3DSSDK.config({
                jwt: "{{$authentication['jwt']}}", // <== jwt from server
                form: "cartForm",
                challengeiframe: "challengeiframe",
            });

            setTimeout(function () {
                PP3DSSDK.fire3ds().then(
                  (data3ds) => {
                    console.log("data3ds",data3ds);
                    $("#idData3Ds").val(JSON.stringify(data3ds.results));
                    $("#cartForm").attr("action",'{{route("prisampay.data")}}')
                    $( "#cartForm" ).submit();
                  },
                  (error) => {
                    console.log("error",error);
                    $("#idData3Ds").val(JSON.stringify(error));
                    $("#cartForm").attr("action",'{{route("prisampay.fail")}}')
                    $( "#cartForm" ).submit();
                  }
                );
            }, 1000);


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
                // setTimeout(function() {
                //     $( "#cartForm" ).submit();
                // }, 15000);
            });
        </script>
    </body>
</html>