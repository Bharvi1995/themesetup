@component('mail::message')
<p style="text-transform: capitalize;">Hi,</p>
<p>You are just a step away from accessing your {{ config('app.name') }} account.</p>
<p>Please login using the below OTP to continue.</p>
<p>OTP : <strong>{{ $login_otp }}</strong></p>
@endcomponent