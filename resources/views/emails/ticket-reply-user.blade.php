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
            <p>We have received a reply to ticket reference number {{ $ticket->id }}<br>Click here to view</p> 
            <table border="0" cellpadding="0" cellspacing="0" class="mcnButtonContentContainer" style="border-collapse: separate !important;border-top-left-radius: 4px;border-top-right-radius: 4px;border-bottom-right-radius: 4px;border-bottom-left-radius: 4px;background-color: #F8F8F8;" align="center">
                <tbody >
                    <tr>
                        <td align="left" valign="middle" class="mcnButtonContent" style="font-family: Arial; font-size: 14px; padding: 18px;background-color:#2d3748">
                            <a class="mcnButton " title="Verify email address" href="{{ $url }}" style="font-family: -apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif,'Apple Color Emoji','Segoe UI Emoji','Segoe UI Symbol';
                                  box-sizing: border-box;
                                  text-decoration: none;
                                  background-color: #6683A9;
                                  padding: 15px 30px;
                                  border-radius: 30px;
                                  line-height: 60px;
                                  color: #FFFFFF;
                                  font-weight: bold;
                                  border: 1px solid #6683A9;">View Ticket</a>
                        </td>
                    </tr>
                </tbody>
            </table>
            <strong>User Name :</strong> {{ $user->name }}<br>
            <strong>Email :</strong> {{ $user->email }}<br>
            <strong>Subject :</strong> {{ $ticket->title }}<br>
            <strong>Message :</strong> {{ $reply }}
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