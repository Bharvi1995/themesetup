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
            <form method="POST" action="{{ route('infipay-bank-select-store') }}" class="form-dark" id="infipayForm">
                @csrf
                <input type="hidden" name="session_id" value="{{ $id }}" />
                <div class="row">
                    <div class="col-lg-12">
                        <label>Choose Payment option</label>
                        <select class="form-control gatewayInput" name="gateway">
                            <option value=""> -- Select Payment Option --</option>
                            @foreach ($paymentOption as $key => $value)
                                <option value="{{ $key }}" {{ old('gateway') == $key ? 'selected' : '' }}>
                                    {{ $value }}</option>
                            @endforeach
                        </select>
                        @error('gateway')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-lg-6" id="bankInput" style="display: none;">
                        <label>Choose Your Bank</label>
                        <select class="form-control" name="bank">
                            <option value=""> -- Select your bank --</option>
                            @foreach ($banks as $bank)
                                <option value="{{ $bank['code'] }}"
                                    {{ old('bank') == $bank['code'] ? 'selected' : '' }}>
                                    {{ $bank['name'] }}</option>
                            @endforeach
                        </select>
                        @error('bank')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-lg-12 mt-2">
                        <div class="d-grid">
                            <button class="btn btn-danger submitButton">Submit</button>
                        </div>

                    </div>
                </div>
            </form>

        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"
        integrity="sha512-3gJwYpMe3QewGELv8k/BX9vcqhryRdzRMxVfq6ngyWXwo03GFEzjsUm8Q7RZcHPHksttq7/GFoxjCVUjkjvPdw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        $(document).ready(function() {
            var oldGateway = "{{ old('gateway') }}";
            if (oldGateway === "IB" || oldGateway === "BT") {
                $(".gatewayInput").parent().removeClass('col-lg-12').addClass('col-lg-6');
                $("#bankInput").show(300)
            }
            // * Listem gateway input changes
            $(document).on('change', '.gatewayInput', function() {
                var gateway = $(this).val();
                if (gateway === "IB" || gateway === "BT") {
                    $(this).parent().removeClass('col-lg-12').addClass('col-lg-6');
                    $("#bankInput").show(300)
                } else {
                    $(this).parent().removeClass('col-lg-6').addClass('col-lg-12');
                    $("#bankInput").hide(300)
                }
            });

            $(document).on('click', '.submitButton', function() {
                $(this).prop("disabled", true)
                $(this).text("Processing...")
                $("#infipayForm").submit()
            });
        });
    </script>
</body>

</html>
