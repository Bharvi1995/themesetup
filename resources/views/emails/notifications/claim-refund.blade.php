@component('mail::message')
<p style="text-transform: capitalize;">Hi,</p>
<p>
A refund request for order number {{ $transaction->order_id }} has been initiated. It may take around 15-20 business days for the refund to be processed and credited in the account.
</p>
<p>
We value your association with us.
</p>
@endcomponent