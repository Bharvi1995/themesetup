<!DOCTYPE html>
<html>
<head>
	<title>Select payment method</title>
</head>
<body>
	<form accept="{{ route('transactionRepo.gatewaySubmit', $session_id) }}" method="post">
		@csrf
		<select name="payment_gateway_id">
			<option value="1">Stripe</option>
			<option value="2">Paypal</option>
		</select>
		<input type="submit" name="Continue" />
	</form>
</body>
</html>