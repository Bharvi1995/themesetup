@component('mail::message')

{{-- Header --}}
@slot('header')
    @component('mail::header', ['url' => ''])
        <h1>{{ config('app.name') }}</h1>
    @endcomponent
@endslot

Dear Administrator,

<strong>{!! $content['company'] !!}</strong> requested your approval for adding a new IP and API key.
<p>Following are the details:</p>

@foreach($content['websites'] as $key => $value)	
<strong>IP Address-</strong> {{ $value['ip_address'] }}
<br>
@endforeach
<br>
<strong>API Key-</strong> {{ $content['api_key'] }}

Feel free to reach out to us for any queries.

{{-- Footer --}}
@slot('footer')
    @component('mail::footer')
         © {{ date('Y') }} {{ config('app.name') }}
    @endcomponent
@endslot

@endcomponent