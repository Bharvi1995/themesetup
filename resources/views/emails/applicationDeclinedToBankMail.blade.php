@component('mail::message')
<p style="text-transform: capitalize;">Hello Admin,</p>
<p>Unfortunately we are not able to onboard {!! $business_name !!} at the moment. Below are the reasons for the decline</p>
<br>
<p><b>Reason:</b> {{ $decline_reason }}</p>
<br>
<p>Thanks,<br> {{ $bank_name }}</p>
@endcomponent