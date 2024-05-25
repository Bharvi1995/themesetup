@component('mail::message')
<p style="text-transform: capitalize;">Hello {{$name}}, </p>
<p>We have found the information provided in your application to be insufficient, we request you to forward us the documents mentioned below so we may continue with our compliance and have you approved at the earliest.</p>

<p><b> Reason :</b> {{ $reason }}</p>
@endcomponent