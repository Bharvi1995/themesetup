@component('mail::message')
<p style="text-transform: capitalize;">Hello Admin,</p>
<p>We have found the information provided in the merchant application for <b>{!! $company_name !!}</b> to be insufficient, we request you to forward us the documents mentioned below so we may continue with our compliance and have the merchant approved at the earliest.</p>

<p><b>Bank Name:</b> {!! $bank_name !!}</p>
<p><b>Company Name:</b> {!! $company_name !!}</p>
<p><b>Merchant Email:</b> {!! $merchant_email !!}</p>
<p><b>Note:</b> {!! $referred_note !!}</p>
<br><br>
<p>Thanks,<br>
{!! $bank_name !!}
</p>
@endcomponent