<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payo callback</title>
</head>

<body>

    <div style="text-align: center;">
        @if ($response['status'] == '1')
            <h1 style="color: green;">Transaction processed successfully</h1>
        @else
            <h1 style="color: yellow;">{{ $response['response'] }}</h1>
        @endif

        <a href="" style="color: orange;">Open My App</a>
    </div>

</body>

</html>
