<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Please do not refresh this page...</title>
</head>

<body onload="document.regForm.submit()">
    <div class="container" style="padding: 2rem;">
        <center>
            <h1>Your Transaction is being processed , Please do not close this window or click the Back button on your browser.</h1>
            <h2>Please wait for <span id="countdown">20</span> seconds...</h2>
        </center>
        <form name="regForm" method="post" action="{{$paymentResponse->ACSUrl}}">
            {{-- @csrf --}}
            <input type="hidden" name="PaReq" value="{{$paymentResponse->Payload}}" />
            <input type="hidden" name="TermUrl" value="{{$paymentResponse->ValidateUrl}}" />
            <input type="hidden" name="MD" value="{{$paymentResponse->requestID}}" />
        </form>
    </div>
</body>

</html>