@component('mail::message')
<p style="text-transform: capitalize;">Hi,</p>
<p>Thank you for choosing {{ config('app.name') }}.</p>
<p>You have successfully registered with us. Please activate your account by clicking the link below</p>
<a href="{{ route('agent-activate',$token) }}" class="custom-btn">Verify your account</a>
@endcomponent