<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Input OTP sent to your registered EMail/Phone number</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
	<div class="container">
		<div class="row">
			<h3>You should have received OTP to your registered Email and Phone number.</h3>
			<form method="post" action="{{ route('korapay.otpSubmit', $session_id) }}">
				@csrf
				<div class="mb-3">
					<label for="otp" class="form-label">Input OTP</label>
					<input type="text" id="otp" name="otp" value="{{ old('otp') }}" class="form-control" placeholder="Input OTP" required>
					@if ($errors->has('otp'))
	                <span class="text-danger">{{ $errors->first('otp') }}</span>
	                @endif
				</div>
				<button type="submit" class="btn btn-primary">Submit</button>
			</form>
		</div>
	</div>
</body>
</html>