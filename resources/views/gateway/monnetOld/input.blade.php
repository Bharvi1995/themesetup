<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <script type="text/javascript" src="{{ storage_asset('themeAdmin/js/jquery-latest.min.js') }}"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        <title>Please fill the form and submit to complete the transaction...</title>
    </head>
    <body>
        <div class="container">
            <h1>Please fill the form and submit to complete the transaction</h1>
            <form method="post" action="{{ route('opay.inputResponse', [$input_type, $session_id, $order_id]) }}">
                {{-- @csrf --}}
                <div class="form-group row">
                    <fieldset>
                        <legend>Bank details:</legend>
                        <div class="col-md-6">
                            <label for="input">Input {{ $input_type }}</label>
                            <input type="text" placeholder="Input {{ $input_type }}" class="form-control" id="input" name="input" />
                            @if ($errors->has('input'))
                                <span class="help-block">
                                    <strong><span class="text-danger">{{ $errors->first('input') }}</span></strong>
                                </span>
                            @endif
                        </div>
                    </fieldset>
                </div>
                <button type="submit" class="btn btn-primary btn-default">Submit</button>
            </form>
        </div>
    </body>
</html>