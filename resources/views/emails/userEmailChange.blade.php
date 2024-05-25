@component('mail::message')
<p style="text-transform: capitalize;">Hi,</p>
<p>We received your email change request.
    Please activate your email change account by clicking on the button below. </p>
<a href="{{ route('user-email-activate', ['token' => $token,'id'=>$id]) }}" class="custom-btn">Verify New Email</a>
@endcomponent