@component('mail::message')
<p style="text-transform: capitalize;">Hi,</p>
<p>A new transaction with below details has been approved:</p>
<p style="padding: 5px 0px; margin: 0px;"><strong>Transaction no. :</strong>{{ $order_id }}</p>
<p style="padding: 5px 0px; margin: 0px;"><strong>Name :</strong>{!! $first_name !!} {!! $last_name !!}</p>
<p style="padding: 5px 0px; margin: 0px;"><strong>Email :</strong> {!! $email !!}</p>
<p style="padding: 5px 0px; margin: 0px;"><strong>Card Number :</strong> {{ $card_no ? substr($card_no, 0, 4) . 'XXXXXXXXXXXX' : '' }}</p>
<p style="padding: 5px 0px; margin: 0px; border-bottom: 2px dotted #c1c1c1;"><strong>Transaction Date :</strong> {!! $created_at !!}</p>
<p style="padding: 5px 0px; margin: 0px; border-bottom: 3px dotted #c1c1c1;"><strong>Amount :</strong> {!! $amount !!} {!! $currency !!}</p>
<p><br> If you have not made this transaction or notice any error please contact us at <strong>{{ config('app.email_support') }}</strong></p>
@endcomponent