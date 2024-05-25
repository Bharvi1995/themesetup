@component('mail::message')
Dear Team,

A new application has been received. 

Kindly reach out to the merchant to get the application processed at the earliest.

@component('mail::button', ['url' => $url])
View
@endcomponent

<p>Email : {{ $email }}</p>
<p>Skype : {{ $skype }}</p>

@endcomponent