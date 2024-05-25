@component('mail::message')
    <strong>Dear Admin</strong>
    <p>I hope this email finds you in good health and spirits. We have received new refund
        in our Portal.Please
        send this refund request to AQ bank for process.
    </p>
    <p>Refund Order Id :- <strong>{{ $transaction->order_id }}</strong></p>
    <p>Refund Reason :- <strong>{{ $transaction->refund_reason }}</strong></p>
@endcomponent
