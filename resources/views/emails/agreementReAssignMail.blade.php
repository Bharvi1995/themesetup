@component('mail::message')
<p style="text-transform: capitalize;">Hi,</p>
<p>Thank you for Submitting your Agreement with {{ config('app.name') }}.</p>
<p>Our team has reviewed the submitted agreement and have requested a re-submission, for the following reasons :</p>
<p><strong>{{ $reason }}</strong></p>
<p>Kindly resubmit the signed agreement as requested by clicking the link below.</p>
<a href="{!! $url !!}" target="_blank" class="custom-btn">Resubmit Agreement</a>
@endcomponent