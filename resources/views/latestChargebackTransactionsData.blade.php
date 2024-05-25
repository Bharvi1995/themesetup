@if(isset($latestChargebackTransactionsData) && count($latestChargebackTransactionsData)>0)
    @foreach($latestChargebackTransactionsData as $chargeback)
        <tr>
            <td>{{ $chargeback->id }}</td>
            <td>{{ $chargeback->order_id }}</td>
            <td>{{ date('d-m-Y', strtotime($chargeback->chargebacks_date)) }}</td>
            <td>{{ $chargeback->card_no }}</td>
            <td>{{ $chargeback->email }}</td>
            <td>{{ $chargeback->amount }}</td>
            <td>{{ $chargeback->currency }}</td>
            <td>{{ $chargeback->descriptor }}</td>
            <td>{{ $chargeback->customer_order_id }}</td>
            <td>
                @if($chargeback->status == '1')
                <label class="Badges Badges-blue">Success</label>
                @elseif($chargeback->status == '2')
                <label class="Badges Badges-yellow">Pending</label>
                @elseif($chargeback->status == '3')
                <label class="Badges Badges-white">Canceled</label>
                @elseif($chargeback->status == '4')
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