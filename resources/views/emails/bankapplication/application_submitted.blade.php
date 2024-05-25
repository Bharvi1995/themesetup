@component('mail::message')
<p style="text-transform: capitalize;">Hello Admin,</p>
<p>A new bank application has been filled out for you to review.</p>
<br>
<p>
	<b>Bank Name:</b> {{ $name }}
</p>
@endcomponent