@if(isset($latestRefundTransactionsData) && count($latestRefundTransactionsData)>0)
    @foreach($latestRefundTransactionsData as $refundData)
        <tr>
            <td>{{ $refundData->id }}</td>
            <td>{{ $refundData->order_id }}</td>
            <td>{{ date('d-m-Y', strtotime($refundData->refund_date)) }}</td>
            <td>{{ $refundData->card_no }}</td>
            <td>{{ $refundData->email }}</td>
            <td>{{ $refundData->amount }}</td>
            <td>{{ $refundData->currency }}</td>
            <td>{{ $refundData->descriptor }}</td>
            <td>{{ $refundData->customer_order_id }}</td>
            <td>
                @if($refundData->status == '1')
                <label class="Badges Badges-blue">Success</label>
                @elseif($refundData->status == '2')
                <label class="Badges Badges-yellow">Pending</label>
                @elseif($refundData->status == '3')
                <label class="Badges Badges-white">Canceled</label>
                @elseif($refundData->status == '4')
                <label class="Badges Badges-white">To Be Confirm</label>
                @else
                <label class="Badges Badges-yellow">Declined</label>
                @endif
            </td>
        </tr>
    @endforeach
@else
<tr>
    <td colspan="10">No Record found!.</td>
</tr>
@endif