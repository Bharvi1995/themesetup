<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ config('app.name') }} | Checkout Form Response </title>
    <link rel="icon" type="image/png" sizes="16x16" href="{{ storage_asset('softtheme/img/favicon.ico') }}">
    <link href="{{ storage_asset('theme/css/style.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ storage_asset('themeAdmin/css/typography.css') }}">
    <link href="{{ storage_asset('themeAdmin/css/custom.css') }}" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&family=Roboto:wght@100;300;400;500;700;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css">
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300|Montserrat' rel='stylesheet' type='text/css'>
    <style type="text/css">
        body{
            background-color: #F8F8F8 !important;
            font-family: 'Poppins', sans-serif !important;
            color: #000;
        }
        p,
        h1{
            color: #000 !important;
        }
        .btn-primary:hover,
        .btn-primary:focus,
        .btn-primary {
            background-color: #6683A9;
            border-color: #6683A9;
            color: #fff;
        }

        .modalbox.success,
        .modalbox.error {
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            -webkit-border-radius: 2px;
            -moz-border-radius: 2px;
            border-radius: 2px;
            background: #fff;
            padding: 25px 25px 15px;
            text-align: center;
        }

        .modalbox.success.animate .icon,
        .modalbox.error.animate .icon {
            -webkit-animation: fall-in 0.75s;
            -moz-animation: fall-in 0.75s;
            -o-animation: fall-in 0.75s;
            animation: fall-in 0.75s;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
        }

        .modalbox.success button,
        .modalbox.error button,
        .modalbox.success button:active,
        .modalbox.error button:active,
        .modalbox.success button:focus,
        .modalbox.error button:focus {
            -webkit-transition: all 0.1s ease-in-out;
            transition: all 0.1s ease-in-out;
            -webkit-border-radius: 30px;
            -moz-border-radius: 30px;
            border-radius: 30px;
            margin-top: 15px;
            width: 80%;
            background: transparent;
            color: #4caf50;
            border-color: #4caf50;
            outline: none;
        }

        .modalbox.success button:hover,
        .modalbox.error button:hover,
        .modalbox.success button:active:hover,
        .modalbox.error button:active:hover,
        .modalbox.success button:focus:hover,
        .modalbox.error button:focus:hover {
            color: #fff;
            background: #4caf50;
            border-color: transparent;
        }

        .modalbox.success .icon,
        .modalbox.error .icon {
            position: relative;
            margin: 0 auto;
            margin-top: -75px;
            background: #4caf50;
            height: 100px;
            width: 100px;
            border-radius: 50%;
        }

        .modalbox.success .icon span,
        .modalbox.error .icon span {
            postion: absolute;
            font-size: 4em;
            color: #fff;
            text-align: center;
            padding-top: 20px;
        }

        .modalbox.error button,
        .modalbox.error button:active,
        .modalbox.error button:focus {
            color: #f44336;
            border-color: #f44336;
        }

        .modalbox.error button:hover,
        .modalbox.error button:active:hover,
        .modalbox.error button:focus:hover {
            color: #fff;
            background: #f44336;
        }

        .modalbox.error .icon {
            background: #f44336;
        }

        .modalbox.error .icon span {
            padding-top: 25px;
        }

        .center {
            float: none;
            margin-left: auto;
            margin-right: auto;
            /* stupid browser compat. smh */
        }

        .center .change {
            clearn: both;
            display: block;
            font-size: 10px;
            color: #ccc;
            margin-top: 10px;
        }

        @-webkit-keyframes fall-in {
            0% {
                -ms-transform: scale(3, 3);
                -webkit-transform: scale(3, 3);
                transform: scale(3, 3);
                opacity: 0;
            }

            50% {
                -ms-transform: scale(1, 1);
                -webkit-transform: scale(1, 1);
                transform: scale(1, 1);
                opacity: 1;
            }

            60% {
                -ms-transform: scale(1.1, 1.1);
                -webkit-transform: scale(1.1, 1.1);
                transform: scale(1.1, 1.1);
            }

            100% {
                -ms-transform: scale(1, 1);
                -webkit-transform: scale(1, 1);
                transform: scale(1, 1);
            }
        }

        @-moz-keyframes fall-in {
            0% {
                -ms-transform: scale(3, 3);
                -webkit-transform: scale(3, 3);
                transform: scale(3, 3);
                opacity: 0;
            }

            50% {
                -ms-transform: scale(1, 1);
                -webkit-transform: scale(1, 1);
                transform: scale(1, 1);
                opacity: 1;
            }

            60% {
                -ms-transform: scale(1.1, 1.1);
                -webkit-transform: scale(1.1, 1.1);
                transform: scale(1.1, 1.1);
            }

            100% {
                -ms-transform: scale(1, 1);
                -webkit-transform: scale(1, 1);
                transform: scale(1, 1);
            }
        }

        @-o-keyframes fall-in {
            0% {
                -ms-transform: scale(3, 3);
                -webkit-transform: scale(3, 3);
                transform: scale(3, 3);
                opacity: 0;
            }

            50% {
                -ms-transform: scale(1, 1);
                -webkit-transform: scale(1, 1);
                transform: scale(1, 1);
                opacity: 1;
            }

            60% {
                -ms-transform: scale(1.1, 1.1);
                -webkit-transform: scale(1.1, 1.1);
                transform: scale(1.1, 1.1);
            }

            100% {
                -ms-transform: scale(1, 1);
                -webkit-transform: scale(1, 1);
                transform: scale(1, 1);
            }
        }

        @-webkit-keyframes plunge {
            0% {
                margin-top: -100%;
            }

            100% {
                margin-top: 25%;
            }
        }

        @-moz-keyframes plunge {
            0% {
                margin-top: -100%;
            }

            100% {
                margin-top: 25%;
            }
        }

        @-o-keyframes plunge {
            0% {
                margin-top: -100%;
            }

            100% {
                margin-top: 25%;
            }
        }

        @-moz-keyframes fall-in {
            0% {
                -ms-transform: scale(3, 3);
                -webkit-transform: scale(3, 3);
                transform: scale(3, 3);
                opacity: 0;
            }

            50% {
                -ms-transform: scale(1, 1);
                -webkit-transform: scale(1, 1);
                transform: scale(1, 1);
                opacity: 1;
            }

            60% {
                -ms-transform: scale(1.1, 1.1);
                -webkit-transform: scale(1.1, 1.1);
                transform: scale(1.1, 1.1);
            }

            100% {
                -ms-transform: scale(1, 1);
                -webkit-transform: scale(1, 1);
                transform: scale(1, 1);
            }
        }

        @-webkit-keyframes fall-in {
            0% {
                -ms-transform: scale(3, 3);
                -webkit-transform: scale(3, 3);
                transform: scale(3, 3);
                opacity: 0;
            }

            50% {
                -ms-transform: scale(1, 1);
                -webkit-transform: scale(1, 1);
                transform: scale(1, 1);
                opacity: 1;
            }

            60% {
                -ms-transform: scale(1.1, 1.1);
                -webkit-transform: scale(1.1, 1.1);
                transform: scale(1.1, 1.1);
            }

            100% {
                -ms-transform: scale(1, 1);
                -webkit-transform: scale(1, 1);
                transform: scale(1, 1);
            }
        }

        @-o-keyframes fall-in {
            0% {
                -ms-transform: scale(3, 3);
                -webkit-transform: scale(3, 3);
                transform: scale(3, 3);
                opacity: 0;
            }

            50% {
                -ms-transform: scale(1, 1);
                -webkit-transform: scale(1, 1);
                transform: scale(1, 1);
                opacity: 1;
            }

            60% {
                -ms-transform: scale(1.1, 1.1);
                -webkit-transform: scale(1.1, 1.1);
                transform: scale(1.1, 1.1);
            }

            100% {
                -ms-transform: scale(1, 1);
                -webkit-transform: scale(1, 1);
                transform: scale(1, 1);
            }
        }

        @keyframes fall-in {
            0% {
                -ms-transform: scale(3, 3);
                -webkit-transform: scale(3, 3);
                transform: scale(3, 3);
                opacity: 0;
            }

            50% {
                -ms-transform: scale(1, 1);
                -webkit-transform: scale(1, 1);
                transform: scale(1, 1);
                opacity: 1;
            }

            60% {
                -ms-transform: scale(1.1, 1.1);
                -webkit-transform: scale(1.1, 1.1);
                transform: scale(1.1, 1.1);
            }

            100% {
                -ms-transform: scale(1, 1);
                -webkit-transform: scale(1, 1);
                transform: scale(1, 1);
            }
        }

        @-moz-keyframes plunge {
            0% {
                margin-top: -100%;
            }

            100% {
                margin-top: 15%;
            }
        }

        @-webkit-keyframes plunge {
            0% {
                margin-top: -100%;
            }

            100% {
                margin-top: 15%;
            }
        }

        @-o-keyframes plunge {
            0% {
                margin-top: -100%;
            }

            100% {
                margin-top: 15%;
            }
        }

        @keyframes plunge {
            0% {
                margin-top: -100%;
            }

            100% {
                margin-top: 15%;
            }
        }

        .modalbox {
            background: #FFFFFF !important;
            box-shadow:0px 2px 5px 0px #05309533 !important;
            border-radius: 5px !important;        
            color: #B3ADAD;
        }
    </style>
</head>

<body class="h-100">
    <div class="container" style="margin-top: 10em;">
        @if (isset($response['responseCode']) && in_array($response['responseCode'], ['0', '3']))
            <div class="row">
                <div class="modalbox error col-sm-8 col-md-6 col-lg-5 center animate">
                    <div class="icon">
                        <span class="glyphicon glyphicon-thumbs-down"></span>
                    </div>
                    <h1>Declined!</h1>
                    <p style="font-weight: 900;">
                        @if (isset($response['responseMessage']))
                            {{ $response['responseMessage'] }}
                        @else
                            Your transaction was declined.
                        @endif
                    </p>
                    <a href="{{ route('iframe.checkout', $token) }}" class="btn btn-primary btn-block">Create New
                        Transaction</a>
                </div>
            </div>
        @elseif(isset($response['responseCode']) && $response['responseCode'] == '1')
            <div class="row">
                <div class="modalbox success col-sm-8 col-md-6 col-lg-5 center animate">
                    <div class="icon">
                        <span class="glyphicon glyphicon-ok"></span>
                    </div>
                    <h1>Success!</h1>
                    <p style="font-weight: 900;">
                        @if (isset($response['responseMessage']))
                            {{ $response['responseMessage'] }}
                        @else
                            Transaction processed successfully.
                        @endif
                    </p>
                    <a href="{{ route('iframe.checkout', $token) }}" class="btn btn-primary btn-block">Create New
                        Transaction</a>
                </div>
            </div>
        @elseif(isset($response['responseCode']) && $response['responseCode'] == '2')
            <div class="row">
                <div class="modalbox success col-sm-8 col-md-6 col-lg-5 center animate">
                    <div class="icon">
                        <span class="glyphicon glyphicon-ok"></span>
                    </div>
                    <h1>Pending!</h1>
                    <p style="font-weight: 900;">
                        @if (isset($response['responseMessage']))
                            {{ $response['responseMessage'] }}
                        @else
                            Transaction pending.
                        @endif
                    </p>
                    <a href="{{ route('iframe.checkout', $token) }}" class="btn btn-primary btn-block">Create New
                        Transaction</a>
                </div>
            </div>
        @elseif(isset($response['responseCode']) && in_array($response['responseCode'], ['5', '6']))
            <div class="row">
                <div class="modalbox error col-sm-8 col-md-6 col-lg-5 center animate">
                    <div class="icon">
                        <span class="glyphicon glyphicon-remove"></span>
                    </div>
                    <h1>Blocked!</h1>
                    <p style="font-weight: 900;">
                        @if (isset($response['responseMessage']))
                            {{ $response['responseMessage'] }}
                        @else
                            Your transaction was blocked.
                        @endif
                    </p>
                    <a href="{{ route('iframe.checkout', $token) }}" class="btn btn-primary btn-block">Create New
                        Transaction</a>
                </div>
            </div>
        @else
            <div class="row">
                <div class="modalbox error col-sm-8 col-md-6 col-lg-5 center animate">
                    <div class="icon">
                        <span class="glyphicon glyphicon-exclamation-sign"></span>
                    </div>
                    <h1>Wrong!</h1>
                    <p style="font-weight: 900;">
                        @if (isset($response['responseMessage']))
                            {{ $response['responseMessage'] }}
                        @else
                            Someting wrong.
                        @endif
                    </p>
                </div>
            </div>
        @endif
    </div>
    <script src="{{ storage_asset('setup/assets/lib/cleave.js/cleave.min.js') }}"></script>
    <script src="{{ storage_asset('theme/vendor/global/global.min.js') }}"></script>
    <script src="{{ storage_asset('theme/js/creditly.js') }}"></script>
</body>

</html>
