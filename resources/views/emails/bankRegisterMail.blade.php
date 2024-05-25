@component('mail::message')
<p style="text-transform: capitalize;">Hi,</p>
<p>Thank you for choosing {{ config('app.name') }}.</p>
<p>You have successfully signed up with us. Kindly verify your registered e-mail address by clicking on the link below.</p>
<a href="{{ route('bank-activate',$token) }}" class="custom-btn">Verify your email</a>
@endcomponent