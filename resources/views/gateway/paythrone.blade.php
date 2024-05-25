<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ config('app.name') }} | Paythrone Page</title>
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

        .js-checkout-widget-btn {
            display: inline-block !important;
            padding: 6px 12px !important;
            margin-bottom: 0 !important;
            font-size: 14px !important;
            font-weight: 400 !important;
            line-height: 1.42857143 !important;
            text-align: center !important;
            white-space: nowrap !important;
            vertical-align: middle !important;
            -ms-touch-action: manipulation !important;
            touch-action: manipulation !important;
            cursor: pointer !important;
            -webkit-user-select: none !important;
            -moz-user-select: none !important;
            -ms-user-select: none !important;
            user-select: none !important;
            background-image: none !important;
            border: 1px solid transparent !important;
            border-radius: 4px !important;
            color: #fff !important;
            background-color: #d9534f !important;
            border-color: #d43f3a !important;
            -webkit-appearance: button !important;
            cursor: pointer !important;
        }

        .btn-danger {
            margin-left: 15px;
        }

        .js-checkout-widget-btn,
        .btn-danger {
            width: calc(50% - 10px) !important;
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

        <div class="row" style="">
            <div class="col-md-12">
                <div class="col-md-4 col-md-offset-4 text-center page-background">
                    <img style="max-width: 60%;margin-top: 10px;" src="{{ storage_asset('NewTheme/images/Logo.png') }}" />
                    <br /><br />
                    <script src="https://checkout.thenoy.com/widget/checkout.js" class="js-checkout-widget "
                        data-project="{{$data['project_key']}}" data-user="{{$data['user']}}"
                        data-display-name="{{$data['name']}}" data-price="{{$data['price']}}"
                        data-order-id="{{$data['order_id']}}" data-currency="{{$data['currency']}}">
                    </script>
                    <a class="btn btn-danger" href="{{route('paythrone-redirect', $session_id)}}">Cancel</a>

                </div>
            </div>
        </div>
        @endif
    </div>
</body>
<script src="//code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script type="text/javascript">
    Pay.Api.Checkout.on('success', function() {
            // document.querySelector('a').click();
        });
        Pay.Api.Checkout.on('fail', function() {
            // document.querySelector('a').click();
        });
        Pay.Api.Checkout.on('close', function() {
            document.querySelector('a').click();
        });
</script>

</html>