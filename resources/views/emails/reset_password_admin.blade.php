@component('mail::message')
<p style="text-transform: capitalize;">Hi,</p>
<p>Please click the link below to reset your password.</p>
<a href="{{ route('admin-password-reset-form', $content)}}" class="custom-btn">Reset password</a>
@endcomponent