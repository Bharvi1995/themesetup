@component('mail::message')

{{-- Header --}}
@slot('header')
    @component('mail::header', ['url' => ''])
        <h1>{{ config('app.name') }}</h1>
    @endcomponent
@endslot

<p style="text-transform: capitalize;">Hi,</p>
<p>
Recently IP address has been updated successfully. Please find the details below:-
</p>

<strong>IP Address-</strong> {!! $content['data'] !!}<br>
<strong>API Key-</strong> {!! $content['api_key'] !!}<br>
<strong>Status-</strong> Approved

Feel free to reach, if got any queries.

{{-- Footer --}}
@slot('footer')
    @component('mail::footer')
         © {{ date('Y') }} {{ config('app.name') }}
    @endcomponent
@endslot

@endcomponent