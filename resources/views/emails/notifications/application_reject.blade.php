@component('mail::message')
<p style="text-transform: capitalize;">Hi,</p>

<p>Greetings from {{ config('app.name') }},</p>
<p>Thank you for submitting your application with {{ config('app.name') }}. We regret to inform you that we are unable to approve your application at this moment.</p>
<p>We appreciate your interest shown in {{ config('app.name') }}.</p>
@endcomponent