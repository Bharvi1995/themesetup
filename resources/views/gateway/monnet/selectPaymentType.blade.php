<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Select Payment option</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ storage_asset('NewTheme/images/favicon.ico') }}">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('NewTheme/css/bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('NewTheme/css/custom.css') }}">

    <style>
        body {
            background-color: #262626 !important;
            color: #B3ADAD !important;
        }

        .mainDiv {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .formDiv {
            border-radius: 20px;
            background: #2B2B2B;
        }
    </style>
</head>

<body>
    <div class="mainDiv">
        <div class="formDiv  p-2">
            <form method="POST" action="{{ route('monnet.payment.option.form.submit') }}" class="form-dark"
                id="monnetForm">
                @csrf
                <input type="hidden" name="session_id" value="{{ $id }}" />
                <div class="row">
                    <div class="col-lg-4">
                        <label>Choose Payment Method</label>
                        <select class="form-control" name="payinMethod">
                            <option value=""> -- Select Payment Option --</option>
                            @foreach ($paymentOptions as $key => $value)
                                <option value="{{ $key }}" {{ old('payinMethod') == $key ? 'selected' : '' }}>
                                    {{ $value }}</option>
                            @endforeach
                        </select>
                        @error('payinMethod')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-lg-4">
                        <label>Choose Document Type</label>
                        <select class="form-control" name="documentType">
                            <option value=""> -- Select Document Type --</option>
                            @foreach ($documentOptions as $key => $value)
                                <option value="{{ $key }}"
                                    {{ old('documentType') == $key ? 'selected' : '' }}>
                                    {{ $value }}</option>
                            @endforeach
                        </select>
                        @error('documentType')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-lg-4">
                        <label>Type Document Number</label>
                        <input type="text" name="document" placeholder="Type your document number"
                            class="form-control" />
                        @error('document')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-lg-12 mt-2">
                        <div class="d-grid">
                            <button class="btn btn-danger">Submit</button>
                        </div>

                    </div>
                </div>
            </form>

        </div>
    </div>


</body>

</html>
