<table id="latest_transactions" class="table mb-0 table-borderless table-striped">
   <thead>
      <tr>
         <th>Order No.</th>
         <th>Date</th>
         <th>Amount</th>
         <th>Currency</th>
         <th>Status</th>
      </tr>
   </thead>
    <tbody>
        @if(isset($latestTransactionsData) && count($latestTransactionsData)>0)
            @foreach($latestTransactionsData as $allTransaction)
                <tr>
                    <td>{{ $allTransaction->order_id }}</td>
                    <td>{{ convertDateToLocal($allTransaction->created_at, 'd-m-Y') }}</td>
                    <td>{{ $allTransaction->amount }}</td>
                    <td>{{ $allTransaction->currency }}</td>
                    <td>
                        @if($allTransaction->status == '1')
                            <label class="badge badge-success">Success</label>
                        @elseif($allTransaction->status == '2')
                            <label class="badge badge-warning">Pending</label>
                        @elseif($allTransaction->status == '3')
                            <label class="badge badge-yellow">Canceled</label>
                        @elseif($allTransaction->status == '4')
                            <label class="badge badge-primary">To Be Confirm</label>
                        @elseif($allTransaction->status == '5')
                            <label class="badge badge-primary">Blocked</label>
                        @else
                            <label class="badge badge-danger">Declined</label>
                        @endif
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
               <td colspan="10">No Record found!.</td>
            </tr>
        @endif
    </tbody>
</table>