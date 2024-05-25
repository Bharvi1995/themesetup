@component('mail::message')
<p style="text-transform: capitalize;">Hello {{ $bank_name }},</p>
<p>Please find the requested information attached for the merchant application.</p>

<p>
<b>Merchant company name :</b> {{ $company_name }}<br>
<b>Note :</b> {{ $referred_note }}<br>
<b>Note Reply :</b> {{ $referred_note_reply }}
</p>
<p>
	Thanks,<br>
	Team {{ config('app.name') }}
</p>
@endcomponent