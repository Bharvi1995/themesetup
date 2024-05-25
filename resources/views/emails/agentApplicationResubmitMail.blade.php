@component('mail::message')
<p style="text-transform: capitalize;">Hello,</p>
<p>Application Resubmitted!</p>
<p>Application ID : {{$id}}</p>
<p>Company Name : {{$company_name}}</p>
<p>Name : {{$agent_name}}</p>
<p>Email : {{$agent_email}}</p>
@endcomponent