@component('mail::message')
<p style="text-transform: capitalize;">Hi,</p>
<p>Greetings from {{ config('app.name') }}!</p>
<p>Please find attached the System Generated Commission Report for your Account.</p>
<p>Kindly use the link below to update your bank details in order to proceed with the settlement.</p>
<a href="{{ route('agent.bank.details') }}" class="custom-btn">Bank Details</a>
@endcomponent