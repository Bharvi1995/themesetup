@component('mail::message')
<p style="text-transform: capitalize;">Dear admin, </p>
<p>Hope everything is going well from your end!</p>
<p>Please find the below information of a bank who just resubmitted the application:</p>
<br><br>
<p>
	<b>Bank name:</b> {{ $name }} <br>
	<b>Address:</b> {{ $company_address }} <br>
	<b>Phone number:</b> {{ $phone_number }} <br>
	<b>Email address:</b> {{ $email }} 

</p>
<br>
<br>
<p>Please look through this for approval.</p>
@endcomponent