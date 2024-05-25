@component('mail::message')
<p style="text-transform: capitalize;">Dear Esteemed client,</p>
<p>We trust this message finds you in good health. Your {{ config('app.name') }} account is nearly ready for use. To proceed with the application process, kindly use the One-Time Password (OTP) provided below:</p>

<p>OTP : <strong>{{ $user->otp }}</strong></p>

<p>Thank you for selecting <strong>{{ config('app.name') }}</strong> as your preferred payment processing partner. Should you have any questions or concerns, our dedicated customer support team is ready to assist you.</p>
@endcomponent