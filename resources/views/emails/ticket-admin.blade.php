@component('mail::layout')

{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => ''])
<h1>{{ config('app.name') }}</h1>
@endcomponent
@endslot

@slot('subcopy')
<table role="presentation" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td>
            <p>Hi&nbsp;</p>
            <p>A new ticket has been Raised.</p>
            <p>Ticket reference number is {{ $ticket->id }}, please keep this handy for future references.</p>
            <br>
            <strong>User Name :</strong> {{ $user->name }}<br>
            <strong>Email :</strong> {{ $user->email }}<br>
            <strong>Subject :</strong> {{ $ticket->title }}<br>
            <strong>Message :</strong> {{ $ticket->description }}
        </td>
    </tr>
</table>
@endslot

{{-- Footer --}}
@slot('footer')
@component('mail::footer')
© {{ date('Y') }} {{ config('app.name') }}
@endcomponent
@endslot

@endcomponent