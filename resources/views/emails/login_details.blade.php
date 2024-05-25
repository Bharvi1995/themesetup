@component('mail::message')
<p style="text-transform: capitalize;">Congratulations! Your {{ config('app.name') }} account has just been created.</p>
<p>Link to log in: <a href="{{ config('app.url') }}/login">{{ config('app.url') }}/login</a></p>
<p>User Name : {{$content['email']}}</p> 
<p>Password : {{$content['password']}}</p> 
@endcomponent