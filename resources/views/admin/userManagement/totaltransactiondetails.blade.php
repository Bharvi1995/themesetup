<div class="table-responsive">
	@foreach($totalAmount as $totalAmountKey => $totalAmountValue)
		<p><strong>{{ $totalAmountKey }}</strong></p>
		<table class="table table-striped">
			<tr>
				<th>Success</th>
				<th>Declined</th>
				<th>Chargebacks</th>
				<th>Refund</th>
				<th>Flagged</th>
				<th>Success Amount</th>
				<th>Declined Amount</th>
				<th>Chargebacks Amount</th>
				<th>Refund Amount</th>
				<th>Flagged Amount</th>
			</tr>
			<tr>
				@foreach($totalAmountValue as $key => $value)
				    <td>{{ $value }}</td>
				@endforeach
			</tr>	
		</table>
	@endforeach
</div>