@component('mail::message')
<p style="text-transform: capitalize;">Hi,</p>
<p>You are receiving this email because we have received a password reset request for your account. 
</p>
<a class="custom-btn" href="{{ route('rp-password-reset-form', $content)}}">Reset password</a>
<p>If you did not request a password reset, no further action is required </p>
@endcomponent