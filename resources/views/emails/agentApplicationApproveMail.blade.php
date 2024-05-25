@component('mail::message')
<p style="text-transform: capitalize;">Hello,</p>
<p>Greetings of the day !</p>
<p>Congratulations.!!</p>
<p>Your {{ config('app.name') }} application has been pre-approved.</p>
<p>Please click the link below for further details.</p>
<a href="{{ route('rp/login') }}" class="custom-btn">Login</a>
@endcomponent