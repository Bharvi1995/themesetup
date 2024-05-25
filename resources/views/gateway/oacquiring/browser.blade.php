<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>Please do not refresh the page...</title>
</head>
<body>
	<center><h1>Please do not refresh the page...</h1></center>
	<form action="{{ $action_url }}" method="{{ $method }}" id="browserInfoForm">
		@foreach($parameters as $param)
			<input type="hidden" name="{{ $param['name'] }}" value="{{ $param['value'] }}">
		@endforeach
	</form>
	<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#browserInfoForm').submit();
        });
    </script>
</body>
</html>