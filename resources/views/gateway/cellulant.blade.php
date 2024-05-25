<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <title>{{ config('app.name') }} | Cellulant Page</title>
        <!-- Favicon icon -->
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
            .tingg-express-checkout-button {
                float: left;
                padding: 3px 0 0 0;
            }
            .tingg-express-checkout-button-text {
                display: initial !important;
                padding: 8px 12px !important;
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
                margin: 0 !important;
                width: calc(50% - 10px) !important;
            }
            .tingg-express-checkout-button-brand{
                display: none !important;
            }
            .btn-danger {
                display: block;
                width: calc(50% - 10px) !important;
                margin-left: calc(50% + 10px) !important;
            }
        </style>
    </head>
    <body>
        <div class="container" style="padding-top: 12%;">
            @if(isset($error) && !empty($error))
                 <div class="row">
                <div class="col-md-12" >
                    <div class="col-md-4 col-md-offset-4 text-center page-background">
                        <img style="max-width: 60%;margin-top: 10px;" src="{{ storage_asset('NewTheme/images/Logo.png') }}"/>
                        <br/><br/>
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
                <div class="col-md-12" >
                    <div class="col-md-4 col-md-offset-4 text-center page-background">
                        <img style="max-width: 60%;margin-top: 10px;" src="{{ storage_asset('NewTheme/images/Logo.png') }}"/>
                        <br/><br/>
                        <button class="awesome-checkout-button" id="pay"></button>
                        <a class="btn btn-danger" href="{{route('paythrone-redirect', $session_id)}}">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
        <script src="//code.jquery.com/jquery-3.3.1.min.js"></script>
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script src="https://developer.tingg.africa/checkout/v2/tingg-checkout.js"></script>
        <script type="text/javascript">
            // Render the checkout button
            Tingg.renderPayButton({
              className: 'awesome-checkout-button', 
              checkoutType:'redirect'
            });
            
            document.querySelector(".awesome-checkout-button").addEventListener("click", function() {        
              //create the checkout request
              Tingg.renderCheckout({        
                merchantProperties: {           
                  params: "<?php echo $payload['encrypt']; ?>",
                  accessKey : "<?php echo $payload['access_key']; ?>",            
                  countryCode: "<?php echo $payload['country']; ?>",       
                },checkoutType: 'redirect' // or modal 
              });
            });
            setTimeout(function() { 
                   $('#pay').trigger('click');
            }, 1000);
        </script>
        @endif
    </body>
   
</html>