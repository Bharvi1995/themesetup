@component('mail::message')
<p style="text-transform: capitalize;">Hello {{$name}}, </p>
<p>We’re glad to inform you that your application has been approved. You can login to your account through the link below using the details filled at the time of registration.</p>
<a href="{{ route('bank/login') }}" class="custom-btn">Login</a>
@endcomponent