<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Emulator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
</head>
<body>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-5">

            <div class="mt-4">
                <form action="{{ $response_data['redirect_url'] }}" id="payment_form" method="{{ $response_data['redirect_method'] }}">

                    @foreach ($response_data['redirect_params'] as $k=>$redirect_params)
                        <input type="hidden" name="{{ $k }}" value="{{ $redirect_params }}">
                    @endforeach

                        <button type="submit"  id="submit_btn"></button>

                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
<script>
    window.onload = function(){
        document.forms['payment_form'].submit();
    }
</script>