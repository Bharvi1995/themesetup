<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Input Card PIN</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
	<div class="container">
		<div class="row">
			<h3>Input Card PIN number for Credit/Debit Card</h3>
			<form method="post" action="{{ route('korapay.pinSubmit', $session_id) }}">
				@csrf
				<div class="mb-3">
					<label for="pin" class="form-label">Input PIN</label>
					<input type="text" id="pin" name="pin" value="{{ old('pin') }}" class="form-control" placeholder="Input PIN" required>
					@if ($errors->has('pin'))
	                <span class="text-danger">{{ $errors->first('pin') }}</span>
	                @endif
				</div>
				<button type="submit" class="btn btn-primary">Submit</button>
			</form>
		</div>
	</div>
</body>
</html>