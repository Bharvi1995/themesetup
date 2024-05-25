@component('mail::message')
<p>Hello, Best wishes for the day!</p>
<p>We regret to inform you that we were not able to approve your application due to mandatory reasons/missing documentation.</p>
<p>Please log in to your dashboard and complete the necessary steps so the application may be evaluated.</p>

<p><b> Reason </b> : {{$reassign_reason}}</p>
<p>Thank you for your assistance.</p>
@endcomponent