<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ config('app.name') }} | Takepayment Page</title>
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
                    <form action="{{$data['redirect']}}" method="post" style="margin-top: 0.5em;" name="paymentForm">
                        <input type="hidden" name="merchantID" value="{{$data['merchantID']}}" />
                        <input type="hidden" name="action" value="{{$data['action']}}" />
                        <input type="hidden" name="type" value="{{$data['type']}}" />
                        <input type="hidden" name="currencyCode" value="{{$data['currencyCode']}}" />
                        <input type="hidden" name="countryCode" value="{{$data['countryCode']}}">
                        <input type="hidden" name="amount" value="{{$data['amount']}}" />
                        <input type="hidden" name="orderRef" value="{{$data['orderRef']}}" />
                        <input type="hidden" name="transactionUnique" value="{{$data['transactionUnique']}}" />
                        <input type="hidden" name="callbackURL" value="{{$data['callbackURL']}}" />
                        <input type="hidden" name="redirectURL" value="{{$data['redirectURL']}}" />
                        <input type="hidden" name="signature" value="{{$data['signature']}}" />
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