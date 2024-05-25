<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <title>{{ config('app.name') }} | Peach Payment</title>
        <!-- Favicon icon -->
        <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" rel="stylesheet"/>
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"/>
        <style type="text/css" media="screen">
            body {
                background-color: #F9F9F9;
            }
            .form-group label{
                color: #000;
            }
            .page-background {
                padding: 2em;
                box-shadow: 0 0 35px 0 rgb(154 161 171 / 15%);
                border-radius: 5px;
                background-color: #ffffff;
            }
            .black-btn{
                background-color: #000;
                color: #fff;
            }
            .form-control{
                border-radius: 0px;
            }
        </style>
    </head>
    <body>
        <div class="container" style="padding-top: 12%;">
            @if(isset($error) && !empty($error))
                 <div class="row">
                <div class="col-md-12" >
                    <div class="col-md-4 col-md-offset-4 text-center page-background">
                        <br/><br/>
                           <div class="alert alert-danger">
                                <strong>Sorry!</strong> {{$error}}
                            </div>
                    </div>
                </div>
            </div>
            @else
                
                <div class="row">
                    <div class="col-md-12" >
                        <div class="col-md-6 col-md-offset-3 text-center page-background">
                        
                            <br/><br/>
                           <form action="{{ route('peach-callback',$id) }}" class="paymentWidgets" data-brands="VISA MASTER AMEX"></form>
                            </form>
                        </div>
                    </div>
                </div>
        </div>
       
        <script src="{{$url}}/paymentWidgets.js?checkoutId=<?php echo $transaction_response['id']; ?>"></script>

        @endif

    </body>
</html>
