<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Please do not refresh this page...</title>
        <style type="text/css">
            #paymentform{
                display: none;
            }
        </style>
    </head>
    <body>
        <div class="container" style="padding: 2rem;">
            <center>
                <h1>Your Transaction is being processed , Please do not close this window or click the Back button on your browser.</h1>
                <h2>Please wait for <span id="countdown">5</span> seconds...</h2>
            </center>
            <!-- https://www.onlinenaira.com/process.htm -->
            <form action="https://www.onlinenaira.com/process.htm" method="POST" onsubmit = 'document.getElementById("disableBTN").disabled=true; document.getElementById("disableBTN")' id="paymentform" name='paymentform'>
                {{-- @csrf --}}
                <input type="hidden" name=member value="{{$check_assign_mid->member}}">
                <input type="hidden" name=action value="payment">
                <input type="hidden" name=product value="Deposit for Service">
                <input type="hidden" name=country value="{{$input['country']}}">
                <input type="hidden" name=apikey value="{{$check_assign_mid->api_key}}">
                <input type="hidden" name=ureturn value="{{route('onlinenaira.callback',$session_id)}}">
                <input type="hidden" name=unotify value="{{route('onlinenaira.callbacknotify',$session_id)}}">
                <input type="hidden" name=ucancel value="{{route('onlinenaira.cancel',$session_id)}}">
                <input type="hidden" name=comments value="Deposit for Service">
                <input type="hidden" name="user_firstname" value="{{ $input['first_name'] }}">
                <input type="hidden" name="user_lastname" value="{{ $input['last_name'] }}">
                <input type="hidden" name="user_email" value="{{ $input['email'] }}">
                <input type="hidden" name="user_whatsapp" value="{{ $input['phone_no'] }}">
                <input type="hidden" name="payment_options" value="card">
                <div class="pd-25">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>
                                    Price<strong class="text-danger">*</strong>
                                </label>
                                <input type=text name=price value="{{$input['converted_amount']}}" class="form-control fld-txt" readonly="">
                            </div>
                        </div>
                    </div>
                    <div class="common-btns fl-btns">
                        <button type="submit" class="black-btn btn btn-block" id="disableBTN">Submit</button>
                        <a href="{{route('onlinenaira.cancel',$session_id)}}" class="btn btn-block cancel-btn">Cancel</a>
                    </div>
                </div>

                <div class="card-type"></div>
            </form>
        </div>
        <script type="text/javascript" src="{{ storage_asset('themeAdmin/js/jquery-latest.min.js') }}"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                // 5 seconds
                var timeleft = 5;
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
                setTimeout(function() {
                    document.paymentform.submit();
                }, 5000);
            });
        </script>
    </body>
</html>