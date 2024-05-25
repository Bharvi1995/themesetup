<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Please do not refresh this page...</title>
</head>

<body>

    <form method="post" action="https://pay.wonderlandpay.com/TestInterface" id="paymentform"
        name='paymentform'>
        <input type="hidden" name="merNo" value="{{ $check_assign_mid->mid_number }}" />
        <input type="hidden" name="gatewayNo" value="{{ $check_assign_mid->gateway_no }}" />
        <input type="hidden" name="orderNo" value="{{ $input['session_id'] }}" />
        <input type="hidden" name="orderCurrency" value="{{ $input['converted_currency'] }}" />
        <input type="hidden" name="orderAmount" value="{{ $input['converted_amount'] }}" />
        <input type="hidden" name="returnUrl" value="{{ route('wonderland-return', $input['session_id']) }}">
        {{-- <input type="hidden" name="notifyUrl" value="{{ route('wonderlandvisa.notify', $input['session_id']) }}"> --}}
        <input type="hidden" name="notifyUrl" value="https://webhook.site/b1c92bd7-09ec-4fa4-8746-1cf92660f13e">
        <input type="hidden" name="firstName" value="{{ $input['first_name'] }}" />
        <input type="hidden" name="lastName" value="{{ $input['last_name'] }}" />
        <input type="hidden" name="email" value="{{ $input['email'] }}" />
        <input type="hidden" name="ip" value="{{ $input['ip_address'] }}" />
        <input type="hidden" name="phone" value="{{ $input['phone_no'] }} " />
        <input type="hidden" name="country" value="{{ $input['country'] }}" />
        <input type="hidden" name="state" value="{{ $input['state'] }}" />
        <input type="hidden" name="city" value="{{ $input['city'] }}" />
        <input type="hidden" name="address" value="{{ $input['address'] }}" />
        <input type="hidden" name="zip" value="{{ $input['zip'] }}" />
        <input type="hidden" name="signInfo" value="{{ $signInfo }}" />
        <input type="hidden" name="random" value="{{ $input['session_id'] }}" />
    </form>

    <script>
        var form = document.querySelector("#paymentform")
        form.submit();
    </script>
</body>

</html>
