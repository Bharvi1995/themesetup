<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
    </head>
    <body onload="document.form.submit();">
        <form name="form" action="{{ $response_data['threeDSecure']['acsUrl'] }}" target="_self" method="POST">
            <input type="hidden" name="TermUrl" value="{{ route('payGenius.redirect', $session_id) }}" />
    		<input type="hidden" name="MD" value="{{ $response_data['threeDSecure']['transactionId'] }}"/>
    		<input type="hidden" name="connector" value="THREEDSECURE"/>
    		<input type="hidden" name="PaReq" value="{{ $response_data['threeDSecure']['paReq'] }}" />
            <noscript>
            <input type="submit" value="Click here to continue" />
            </noscript>
        </form>
    </body>
</html>