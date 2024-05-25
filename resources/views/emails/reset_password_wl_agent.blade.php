@component('mail::message')
<p style="text-transform: capitalize;">Hi,</p>
<p>You are receiving this email because we received a password reset request for your account.
</p>
<a class="custom-btn" href="{{ route('wl-rp-password-reset-form', $content)}}">Reset password</a>
<p>This password reset link will expire in 60 minutes. </p>
<p>If you did not request a password reset, no further action is required. </p>
@endcomponent