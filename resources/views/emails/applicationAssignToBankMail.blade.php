@component('mail::message')
<p style="text-transform: capitalize;">Hi, {!! $bank_name !!}</p>
<p>A merchant application has been recently assigned to you. Please go through the information and let us know if the merchant is suitable for you to onboard.</p>

<p><b>Merchant Contact Name :</b> {{ $userName }}</p>
<p><b>Company Name :</b> {{ $business_name }}</p>
<p><b>Email :</b> {{ $email }}</p>
@endcomponent