<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ config('app.name') }} | TrustSpay Payment</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ storage_asset('NewTheme/images/favicon.ico') }}">
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
    <style type="text/css" media="screen">
        body {
            background-color: #F9F9F9;
        }

        .form-group label {
            color: #000;
        }

        .page-background {
            padding: 2em;
            box-shadow: 0 0 35px 0 rgb(154 161 171 / 15%);
            border-radius: 5px;
            background-color: #ffffff;
        }

        .black-btn {
            background-color: #000;
            color: #fff;
        }

        .form-control {
            border-radius: 0px;
        }
    </style>
</head>

<body>
    <div class="container" style="padding-top: 12%;">
        @if(isset($error) && !empty($error))
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-4 col-md-offset-4 text-center page-background">
                    <img style="max-width: 60%;margin-top: 10px;" src="{{ storage_asset('NewTheme/images/Logo.png') }}" />
                    <br /><br />
                    <div class="alert alert-danger">
                        <strong>Sorry!</strong> {{$error}}
                    </div>
                </div>
            </div>
        </div>
        @else
        <center>
            <h1>Please do not refresh this page...</h1>
        </center>
        <div class="row" style="display: none;">
            <div class="col-md-12">
                <div class="col-md-4 col-md-offset-4 text-center page-background">
                    <img style="max-width: 60%;margin-top: 10px;" src="{{ storage_asset('NewTheme/images/Logo.png') }}" />
                    <br /><br />
                    <form action="{{route('trustspay-confirmation-submit')}}" method="post" id="paymentForm"
                        name="paymentForm">
                        @csrf
                        <input type="hidden" name="session_id" value="{{$session_id}}" />
                        <input type="hidden" name="merNo" value="{{$input['merNo']}}" />
                        <input type="hidden" name="gatewayNo" value="{{$input['gatewayNo']}}" />
                        <input type="hidden" name="orderNo" value="{{$input['orderNo']}}" />
                        <input type="hidden" name="orderCurrency" value="{{$input['currency']}}" />
                        <input type="hidden" name="orderAmount" value="{{$input['amount']}}" />
                        <input Type="hidden" name="returnUrl" value="{{ $_SERVER['PHP_SELF'] }}" />
                        <input type="hidden" name="cardNo" value="{{$cardDetails[0]}}" />
                        <input type="hidden" name="cardExpireMonth" value="{{$cardDetails[2]}}" />
                        <input type="hidden" name="cardExpireYear" value="{{$cardDetails[1]}}" />
                        <input type="hidden" name="cardSecurityCode" value="{{$cardDetails[3]}}" />
                        <input type="hidden" name="ip" value="{{$input['ip_address']}}" />
                        <input type="hidden" name="issuingBank" value="{{ config('app.name') }}">
                        <input type="hidden" name="csid" value="" id="csid" name="csid" />
                        <input type="hidden" name="firstName" value="{{$input['first_name']}}" />
                        <input type="hidden" name="lastName" value="{{$input['last_name']}}" />
                        <input type="hidden" name="email" value="{{$input['email']}}" />
                        <input type="hidden" name="phone" value="{{$input['phone_no']}}" />
                        <input type="hidden" name="country" value="{{$input['country']}}" />
                        <input type="hidden" name="state" value="{{$input['state']}}" />
                        <input type="hidden" name="city" value="{{$input['city']}}" />
                        <input type="hidden" name="address" value="{{$input['address']}}" />
                        <input type="hidden" name="zip" value="{{$input['zip']}}" />
                        <input type="hidden" name="signInfo" value="{{$input['signinfo']}}" />
                        <input type="submit" name="submit" value="pay" id="pay">
                    </form>
                </div>
            </div>
        </div>
        @endif
    </div>

    <script type="text/javascript" src="{{ storage_asset('themeAdmin/js/jquery-latest.min.js') }}"></script>

    <script type='text/javascript' charset='utf-8'>
        $(document).ready(function() { 

                var src = 'https://shoppingingstore.com/pub/sps.js';
                var script = document.createElement('script');

                script.type = 'text/javascript';
                script.src = src;
                document.body.appendChild(script);
                setTimeout(function() { 
                   $('#pay').trigger('click');
               }, 1000);
                
            });
            
    </script>


</body>

</html>