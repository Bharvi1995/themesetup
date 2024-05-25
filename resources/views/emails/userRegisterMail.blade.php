@component('mail::message')
<p style="text-transform: capitalize;">Dear Client,</p>
<p>
	Thank you for choosing {{ config('app.name') }}! Your Merchant Account application is approved by clicking on the link provided below:
</p>
<a href="{{ route('user-activate',$token) }}" class="custom-btn text-center">Verify Your Email</a>

<p>If you need assistance, our customer support is ready to help. We appreciate your trust and look forward to serving you with excellence</p>
@endcomponent