<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ config('app.name') }} | Trust Payment Confirmation Page</title>
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
        <center>
            <h1>Please do not refresh this page...</h1>
        </center>
        <div class="row" style="display:none">
            <div class="col-md-12">
                <div class="col-md-4 col-md-offset-4 text-center page-background">
                    <img style="max-width: 60%;margin-top: 10px;" src="{{ storage_asset('NewTheme/images/Logo.png') }}" />
                    <br /><br />

                    @if(isset($error) && !empty($error))
                    <div class="alert alert-danger">
                        <strong>Sorry!</strong> {{$error}}
                    </div>
                    @else
                    <form action="https://payments.securetrading.net/process/payments/choice" method="post"
                        style="margin-top: 0.5em;" name="paymentForm">
                        {{-- @csrf --}}
                        <input type="hidden" name="sitereference" value="{{$sitereference}}">
                        <!--siterederence cride-->
                        <input type="hidden" name="stprofile" value="default">
                        <input type="hidden" name="currencyiso3a" value="{{$input['currency']}}">
                        <input type="hidden" name="mainamount" value="{{$input['amount']}}">
                        <input type="hidden" name="version" value="2">

                        <!--Other fields-->
                        <input type="hidden" name="billingpostcode" value="{{$input['zip']}}">
                        <input type="hidden" name="billingfirstname" value="{{$input['first_name']}}">
                        <input type="hidden" name="billinglastname" value="{{$input['first_name']}}">
                        <input type="hidden" name="billingtown" value="{{$input['city']}}">
                        <input type="hidden" name="billingcounty" value="{{$input['country']}}">
                        <input type="hidden" name="billingemail" value="{{$input['email']}}">
                        <input type="hidden" name="billingtelephone" value="{{$input['phone_no']}}">

                        <!--This enables the succes URL rule-->
                        <input type=hidden name="ruleidentifier" value="STR-6">
                        <!--Success URL destination-->
                        <input type=hidden name="successfulurlredirect" value="{{route('trust-success', $session_id)}}">

                        <!--This enables the declined URL rule-->
                        <input type=hidden name="ruleidentifier" value="STR-7">
                        <!--Declined URL destination-->
                        <input type=hidden name="declinedurlredirect" value="{{route('trust-decline', $session_id)}}">

                        <!--Enables rule that redirects the customer following an error-->
                        <input type=hidden name="ruleidentifier" value="STR-13">
                        <!--URL for the errorredirect-->
                        <input type=hidden name="errorurlredirect" value="{{route('trust-fail', $session_id)}}">

                        <!--This enables the all URL notification rule-->
                        <input type=hidden name="ruleidentifier" value="STR-10">
                        <!--All URL notification destination-->
                        <input type=hidden name="allurlnotification"
                            value="{{route('trust-notification', $session_id)}}">

                        <p>Please click on continue to proceed for payment.</p>
                        <br />
                        <button class="btn btn-md btn black-btn btn-block btn-rounded">Continue</button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="{{ storage_asset('themeAdmin/js/jquery-latest.min.js') }}"></script>
    <script type="text/javascript">
        $().ready(function () {
                //form submit
                document.paymentForm.submit();
            });
    </script>
</body>

</html>