<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ config('app.name') }} | Wonderland Page</title>
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
        <div class="row" style="display:none">
            <div class="col-md-12">
                <div class="col-md-4 col-md-offset-4 text-center page-background">
                    <img style="max-width: 60%;margin-top: 10px;" src="{{ storage_asset('NewTheme/images/Logo.png') }}" />
                    <br /><br />
                    <form action="{{$data['action']}}" method="post" style="margin-top: 0.5em;" name="paymentForm">
                        <input type="hidden" name="merNo" value="{{$data['merNo']}}" />
                        <input type="hidden" name="gatewayNo" value="{{$data['gatewayNo']}}" />
                        <input type="hidden" name="orderNo" value="{{$data['orderNo']}}" />
                        <input type="hidden" name="orderCurrency" value="{{$data['orderCurrency']}}" />
                        <input type="hidden" name="orderAmount" value="{{$data['orderAmount']}}" />
                        <input type="hidden" name="returnUrl" value="{{$data['returnUrl']}}" />
                        <input type="hidden" name="notifyUrl" value="{{$data['notifyUrl']}}" />
                        <input type="hidden" name="cardNo" value="{{$data['cardNo']}}" />
                        <input type="hidden" name="cardExpireMonth" value="{{$data['cardExpireMonth']}}" />
                        <input type="hidden" name="cardExpireYear" value="{{$data['cardExpireYear']}}" />
                        <input type="hidden" name="cardSecurityCode" value="{{$data['cardSecurityCode']}}" />
                        <input type="hidden" name="firstName" value="{{$data['firstName']}}" />
                        <input type="hidden" name="lastName" value="{{$data['lastName']}}" />
                        <input type="hidden" name="email" value="{{$data['email']}}" />
                        <input type="hidden" name="phone" value="{{$data['phone']}} " />
                        <input type="hidden" name="country" value="{{$data['country']}}" />
                        <input type="hidden" name="state" value="{{$data['state']}}" />
                        <input type="hidden" name="city" value="{{$data['city']}}" />
                        <input type="hidden" name="address" value="{{$data['address']}}" />
                        <input type="hidden" name="zip" value="{{$data['zip']}}" />
                        <input type="hidden" name="signInfo" value="{{$data['signInfo']}}" />
                        <input type="hidden" name="random" value="{{$data['random']}}" />
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <script src="//code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script type="text/javascript">
        window.onload = function () {
                document.paymentForm.submit();
            }
    </script>
</body>

</html>