<!DOCTYPE html>
<html>
<head>
	<title>Select payment method</title>
</head>
<body>
	<form action="{{ route('hostedAPI.paymentTypeSubmit', $session_id) }}" method="post">
		@csrf
		<select name="payment_type">
			<option value="card">Card Payment</option>
			<option value="crypto">Crypto</option>
			<option value="bank">Bank Transfer</option>
		</select>
		<input type="submit" name="Continue" />
	</form>
</body>
</html>