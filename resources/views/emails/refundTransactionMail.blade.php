@component('mail::message')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => ''])
            <h1>{!! config('app.name') !!}</h1>
        @endcomponent
    @endslot

    <table role="presentation" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <p style="text-transform: capitalize;">Dear merchant,</p>
            </td>
        </tr>
        <tr>
            <td>
                <p>We hope this message finds you well. We would like to inform you that your refund request with reference
                    number
                    {{ $order_id }} has been processed successfully. The refunded amount of <strong>{{ $amount }}
                        {{ $currency }}</strong> will reflect in your bank account
                    within the next 15 business days</p><br />
                <table border="0" cellpadding="5" cellspacing="0"
                    style="width: 100%; text-align: left; background-color: #f8f8f8; padding: 15px 15px 0px 15px; border-radius: 3px;">
                    <tbody>
                        <tr>
                            <td>
                                <p><strong>Order ID</strong></p>
                            </td>
                            <td>
                                <p><span>{!! $order_id !!}</span></p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p><strong>Name</strong></p>
                            </td>
                            <td>
                                <p><span>{!! $first_name !!} {!! $last_name !!}</span></p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p><strong>Email</strong></p>
                            </td>
                            <td>
                                <p style="color: black;"><span>{!! $email !!}</span></p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p><strong>Card Number</strong></p>
                            </td>
                            <td>
                                <p><span>{!! $card_no !!}</span></p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p><strong>Refund Date</strong></p>
                            </td>
                            <td>
                                <p><span>{!! $refund_date !!}</span></p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p><strong>Transaction Date</strong></p>
                            </td>
                            <td>
                                <p><span>{!! $created_at !!}</span></p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p><strong>Amount</strong></p>
                            </td>
                            <td>
                                <p><span>{!! $amount !!} {!! $currency !!}</span></p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <br />
        <tr>
            <td>
                <p style="margin-top: 10px;">If you have any questions or concerns regarding the refund transaction, please
                    do not hesitate to contact
                    our
                    customer
                    support team.</p><br />
                <p>Thank you for choosing our services.</p><br />
            </td>
        </tr>
    </table>

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            © {{ date('Y') }} {{ config('app.name') }}
        @endcomponent
    @endslot
@endcomponent
