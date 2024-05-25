@component('mail::message')

{{-- Header --}}
@slot('header')
    @component('mail::header', ['url' => ''])
        <h1>{{ config('app.name') }}</h1>
    @endcomponent
@endslot

<div class="top-heading-title"></div>
<div class="main-email-details">
    <p>{!! $body !!}</p>
</div>

{{-- Footer --}}
@slot('footer')
    @component('mail::footer')
         © {{ date('Y') }} {{ config('app.name') }}
    @endcomponent
@endslot

@endcomponent