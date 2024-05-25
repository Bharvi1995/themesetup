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
            <form method="post" action="{{ route('Monnet.transactionResponse', $session_id) }}" id="paymentform" name='paymentform'>
                @csrf
                <div class="form-group row">
                    <fieldset>
                        <legend>Bank details:</legend>
                        <div class="col-md-6">
                            <label for="payinCustomerTypeDocument">Select document type :</label>
                            <select class="form-control" name="payinCustomerTypeDocument" id="payinCustomerTypeDocument" required>
                                <option selected disabled>Select Document type</option>
                                <option value="RUC" {{ old('payinCustomerTypeDocument') == 'RUC' ? 'selected' : '' }}>Tax Id Number(Peru and Ecuador)</option>
                                <option value="DNI" {{ old('payinCustomerTypeDocument') == 'DNI' ? 'selected' : '' }}>Identity Document(Peru and Argentina)</option>
                                <option value="PAS" {{ old('payinCustomerTypeDocument') == 'PAS' ? 'selected' : '' }}>Passport(Peru and Ecuador)</option>
                                <option value="CI" {{ old('payinCustomerTypeDocument') == 'CI' ? 'selected' : '' }}>Identity Document(Ecuador)</option>
                                <option value="CE" {{ old('payinCustomerTypeDocument') == 'CE' ? 'selected' : '' }}>Foreign resident Card</option>
                                <option value="RUT" {{ old('payinCustomerTypeDocument') == 'RUT' ? 'selected' : '' }}>Tax Id Number(Chile)</option>
                                <option value="PP" {{ old('payinCustomerTypeDocument') == 'PP' ? 'selected' : '' }}>Passport(Chile)</option>
                            </select>
                            @if ($errors->has('payinCustomerTypeDocument'))
                                <span class="help-block">
                                    <strong><span class="text-danger">{{ $errors->first('payinCustomerTypeDocument') }}</span></strong>
                                </span>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label for="payinCustomerDocument">Document number :</label>
                            <input type="text" placeholder="10257466" class="form-control" id="payinCustomerDocument" name="payinCustomerDocument" />
                            @if ($errors->has('payinCustomerDocument'))
                                <span class="help-block">
                                    <strong><span class="text-danger">{{ $errors->first('payinCustomerDocument') }}</span></strong>
                                </span>
                            @endif
                        </div>
                    </fieldset>
                </div>
                <button type="submit" class="btn btn-primary btn-default">Submit</button>
            </form>
        </div>

        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                // 
            });
        </script>
    </body>
</html>