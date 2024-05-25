@component('mail::message')
<p style="text-transform: capitalize;">Hello Admin,</p>
<p>We’re glad to inform you that the merchant application for {!! $business_name !!} has been approved.</p>
<br><br>
<p>Thanks,<br>
{!! $bank_name !!}
</p>
@endcomponent