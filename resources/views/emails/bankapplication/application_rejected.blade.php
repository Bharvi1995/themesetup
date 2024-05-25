@component('mail::message')
<p style="text-transform: capitalize;">Hello {{$name}}, </p>
<p>Unfortunately we are not able to onboard your application at the moment. Below are the reasons for the decline</p>
<br>
<p>Reason: <strong>{{ $reason }}</strong>
@endcomponent