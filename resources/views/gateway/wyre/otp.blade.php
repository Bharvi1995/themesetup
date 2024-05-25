<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <title>{{ config('app.name') }} - Test 3D Secure Authentication Page</title>
        <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" rel="stylesheet"/>
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"/>
        <style type="text/css" media="screen">
            .page-background {
                background: whitesmoke;
                border-radius: 10px
            }
        </style>
    </head>
    <body>
        <div class="container" style="padding-top: 12%;">
            <div class="row">
                <div class="col-md-12" >
                    <div class="col-md-4 col-md-offset-4 text-center page-background" style="padding: 1rem;">
                        <img style="max-width: 60%;margin-top: 10px;" src="{{ storage_asset('NewTheme/images/Logo.png') }}"/>
                        <br/><br/>
                        Amount:  {{ $data['amount'] }} {{ $data['currency'] }}
                        <br/>
                        <br/>
                        <form action="{{ route('Wyre.submit') }}" method="POST" onsubmit = 'document.getElementById("disableBTN").disabled=true; document.getElementById("disableBTN")'>
                            <input type="hidden" name="session_id" value="{{ $data['session_id'] }}">
                            <input type="hidden" name="walletOrderId" value="{{ $data['walletOrderId'] }}">
                            <input type="hidden" name="reservation" value="{{ $data['reservation'] }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-8 col-md-offset-2">
                                    <strong class="text-info">Please check your mobile for OTP.</strong>                                
                                    <input type="text" name="sms" class="form-control" placeholder="Please enter the OTP.">
                                </div>
                            </div>
                            <br/>
                            <strong>Note :</strong> Please enter your one time password (Valid for 5 mins)
                            <br/>
                            <br/>
                            <button type="submit" class="btn btn-primary btn-rounded btn-md" id="disableBTN">submit</button>
                            <a href="{{ route('Wyre.cancel', $data['session_id']) }}" class="btn btn-danger btn-rounded btn-md">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript" src="{{ storage_asset('themeAdmin/js/jquery-latest.min.js') }}"></script>
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    </body>
</html>