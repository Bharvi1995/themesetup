@component('mail::message')
<p style="text-transform: capitalize;">Dear Valued Client,</p>
<p>We appreciate your choice of {{ config('app.name') }} as your preferred payment processing partner. Our team has
	reviewed your application and has requested a resubmission for the following reason:</p>
<p>
	<strong>{{ $application->reason_reassign }}</strong>
</p>

<p>To complete the application process, please click on the link below and follow the instructions provided.</p>

@component('mail::button', ['url' => $url])
Application
@endcomponent

<p>We apologize for any inconvenience caused and thank you for your prompt attention to this matter.</p>
<p>Please feel free to contact our dedicated customer support team if you require any further assistance.</p>
<p>Thank you for choosing {{ config('app.name') }}.</p>
@endcomponent