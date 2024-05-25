@php
$currency = ['USD', 'HKD', 'GBP', 'JPY', 'EUR', 'AUD', 'CAD', 'SGD', 'NZD', 'TWD', 'KRW', 'DKK', 'TRL', 'MYR', 'THB',
'INR', 'PHP', 'CHF', 'SEK', 'ILS', 'ZAR', 'RUB', 'NOK', 'AED', 'BRL', 'CLP', 'PEN', 'MXN', 'TND'];
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ config('app.name') }} | Checkout Form</title>
    
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ storage_asset('NewTheme/images/favicon.ico') }}">
    <link href="{{ storage_asset('theme/css/style.css') }}" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&family=Roboto:wght@100;300;400;500;700;900&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="{{ storage_asset('/theme/vendor/select2/css/select2.min.css') }}">

    <style type="text/css">
        .grecaptcha-badge {
            z-index: 1000;
        }

        .authincation-content {
            background-color: #fff;
        }

        .h-100vh {
            min-height: 100vh !important;
        }

        .auth-form .text-danger {
            color: #842e2e !important;
        }

        .form-control:hover,
        .form-control:focus,
        .form-control.active {
            border-color: #E89C86;
        }

        .form-control:hover,
        .form-control:focus,
        .form-control.active,
        .select2-container--default .select2-selection--single .select2-selection__rendered,
        .form-control {
            color: #5a5a5a;
            font-size: 0.875rem;
        }

        label.error-block {
            color: red !important;
        }

        input.error,
        textarea.error,
        select.error {
            border-color: red !important;
        }

        input.mismatch,
        textarea.mismatch,
        select.mismatch {
            border-color: orange !important;
        }

        input.valid,
        textarea.valid,
        select.valid {
            border-color: green !important;
        }

        label.error {
            color: red !important;
        }

        label.mismatch {
            border-color: orange !important;
        }

        label.valid {
            color: green !important;
        }

        #city,
        #state {
            cursor: not-allowed;
        }

        .select2-container .select2-selection--single {
            height: 56px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 55px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 55px;
        }

        .select2-container--default .select2-selection--single {
            border-radius: 1.25rem;
            border: 1px solid #f0f1f5;
        }

        .black-btn {
            background-color: #000;
            color: #fff;
        }

        .cancel-btn {
            border-color: #5a5a5a;
        }
    </style>
</head>

<body class="h-100">
    <div class="authincation h-100">
        <div class="container h-100">
            <div class="row justify-content-center h-100vh align-items-center">
                <div class="col-md-8">
                    <div class="text-center mb-3">
                        <a href="{{ route('login') }}">
                            <img src="{{ storage_asset('NewTheme/images/Logo.png') }}" alt="" width="300px">
                        </a>
                    </div>
                    <div class="authincation-content">
                        <div class="row no-gutters">
                            <div class="col-xl-12">
                                <div class="auth-form">
                                    {!! Form::open(array('route' => 'iframe-test','files' => true, 'onsubmit' =>
                                    'document.getElementById("disableBTN").disabled=true;
                                    document.getElementById("disableBTN")', 'class' => 'validity')) !!}
                                    z<div class="row">
                                        <div class="col-md-6 form-group">
                                            <label class="mb-1">First Name</label>
                                            <div><input type="text" name="first_name" class="form-control"
                                                    id="first_name" placeholder="First Name"
                                                    value="{{ isset($_GET['first_name']) ? $_GET['first_name'] : '' }}"
                                                    {{ isset($_GET['first_name']) ? 'readonly' : '' }} required
                                                    data-missing="This field is required"></div>
                                            @if ($errors->has('first_name'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('first_name') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label class="mb-1">Last Name</label>
                                            <div><input type="text" name="last_name" class="form-control" id="last_name"
                                                    placeholder="Last Name"
                                                    value="{{ isset($_GET['last_name']) ? $_GET['last_name'] : '' }}" {{
                                                    isset($_GET['last_name']) ? 'readonly' : '' }} required
                                                    data-missing="This field is required"></div>
                                            @if ($errors->has('last_name'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('last_name') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                        <div class="col-md-12 form-group">
                                            <label class="mb-1">Email Address</label>
                                            <div><input type="email" name="email" class="form-control" id="email"
                                                    placeholder="Email"
                                                    value="{{ isset($_GET['email']) ? $_GET['email'] : '' }}" {{
                                                    isset($_GET['email']) ? 'readonly' : '' }} required
                                                    data-missing="This field is required"></div>
                                            @if ($errors->has('email'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('email') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                        <div class="col-md-12 form-group">
                                            <label class="mb-1">Phone Number</strong>
                                            </label>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div>
                                                        <select class="select2 form-control" name="country_code" id=""
                                                            required data-missing="This field is required"></select>
                                                    </div>
                                                </div>
                                                <div class="col-md-9">
                                                    <div><input class="form-control" name="phone_no" type="text"
                                                            placeholder="Phone No." id="phone_no"
                                                            value="{{ isset($_GET['phone_no']) ? $_GET['phone_no'] : '' }}"
                                                            {{ isset($_GET['phone_no']) ? 'readonly' : '' }} required
                                                            data-missing="This field is required"></div>
                                                </div>
                                            </div>
                                            <sub class="text-danger">Note : Enter your mobile number, to receive an
                                                OTP.</sub>
                                            @if ($errors->has('country_code'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('country_code') }}</strong>
                                            </span>
                                            @endif
                                            @if ($errors->has('phone_no'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('phone_no') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                        <div class="col-md-12 form-group">
                                            <label class="mb-1">Amount</label>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div><select class="select2 form-control" name="currency"
                                                            id="currency" required
                                                            data-missing="This field is required">
                                                            @if (isset($_GET['currency']) && in_array($_GET['currency'],
                                                            $currency))
                                                            <option value="{{ $_GET['currency'] }}">{{ $_GET['currency']
                                                                }}</option>
                                                            @else
                                                            <option value="USD" selected>USD</option>
                                                            <option value="HKD">HKD</option>
                                                            <option value="GBP">GBP</option>
                                                            <option value="JPY">JPY</option>
                                                            <option value="EUR">EUR</option>
                                                            <option value="AUD">AUD</option>
                                                            <option value="CAD">CAD</option>
                                                            <option value="SGD">SGD</option>
                                                            <option value="NZD">NZD</option>
                                                            <option value="TWD">TWD</option>
                                                            <option value="KRW">KRW</option>
                                                            <option value="DKK">DKK</option>
                                                            <option value="TRL">TRL</option>
                                                            <option value="MYR">MYR</option>
                                                            <option value="THB">THB</option>
                                                            <option value="INR">INR</option>
                                                            <option value="PHP">PHP</option>
                                                            <option value="CHF">CHF</option>
                                                            <option value="SEK">SEK</option>
                                                            <option value="ILS">ILS</option>
                                                            <option value="ZAR">ZAR</option>
                                                            <option value="RUB">RUB</option>
                                                            <option value="NOK">NOK</option>
                                                            <option value="AED">AED</option>
                                                            <option value="BRL">BRL</option>
                                                            <option value="TND">TND</option>
                                                            @endif
                                                        </select></div>
                                                </div>
                                                <div class="col-md-9">
                                                    <div><input class="form-control" name="amount" type="text"
                                                            placeholder="Amount" id="amount"
                                                            value="{{ isset($_GET['amount']) ? $_GET['amount'] : '' }}"
                                                            {{ isset($_GET['amount']) ? 'readonly' : '' }} required
                                                            data-missing="This field is required"></div>
                                                </div>
                                            </div>
                                            @if ($errors->has('amount'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('amount') }}</strong>
                                            </span>
                                            @endif
                                            @if ($errors->has('currency'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('currency') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                        <div class="col-md-12 form-group">
                                            <label class="mb-1">Address</label>
                                            <div><textarea class="form-control" name="address" id="address"
                                                    placeholder="Address" {{ isset($_GET['address']) ? 'readonly' : ''
                                                    }} required
                                                    data-missing="This field is required">{{ isset($_GET['address']) ? $_GET['address'] : '' }}</textarea>
                                            </div>
                                            @if ($errors->has('address'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('address') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label class="mb-1">Country</label>
                                            @if(isset($_GET['country']))ext" name="state" class="form-control" value="{{
                                            isset($_GET['country']) ? $_GET['country'] : '' }}" {{
                                            isset($_GET['country']) ? 'readonly' : '' }} required data-missing="This
                                            field is required">
                                            @else
                                            <div><select class="form-control select2" name="country" id="country11"
                                                    required data-missing="This field is required">
                                                    <option disabled> -- Select Country -- </option>
                                                    @foreach(getCountry() as $key => $value)
                                                    <option value="{{ $key }}">{{ $value }}</option>
                                                    @endforeach
                                                </select></div>
                                            @endif
                                            @if ($errors->has('country'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('country') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label class="mb-1">Zip code</label>
                                            <input class="form-control" name="zip" type="text" id="zip"
                                                placeholder="Zip Code"
                                                value="{{ isset($_GET['zip']) ? $_GET['zip'] : '' }}" {{
                                                isset($_GET['zip']) ? 'readonly' : '' }} required
                                                data-missing="This field is required">
                                            @if ($errors->has('zip'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('zip') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label class="mb-1">City</label>
                                            <input class="form-control" name="city" type="text" id="city"
                                                placeholder="City"
                                                value="{{ isset($_GET['city']) ? $_GET['city'] : '' }}" {{
                                                isset($_GET['city']) ? 'readonly' : '' }} required
                                                data-missing="This field is required" disabled="disabled">
                                            @if ($errors->has('city'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('city') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label class="mb-1">State</label>
                                            <input type="text" name="state" placeholder="State" id="state"
                                                class="form-control"
                                                value="{{ isset($_GET['state']) ? $_GET['state'] : '' }}" {{
                                                isset($_GET['state']) ? 'readonly' : '' }} required
                                                data-missing="This field is required" disabled="disabled">
                                            @if ($errors->has('state'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('state') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                        <div class="col-md-6">
                                            <button type="submit" class="btn black-btn btn-block">Pay Now</button>
                                        </div>
                                        <div class="col-md-6">
                                            <button type="button" id="disableBTN"
                                                class="btn cancel-btn btn-block">Cancel</button>
                                        </div>
                                    </div>
                                    {!! Form::close() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br>


    <script src="{{ storage_asset('theme/vendor/global/global.min.js') }}"></script>
    <script src="{{ storage_asset('theme/vendor/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ storage_asset('theme/js/custom.min.js') }}"></script>
    <script src="{{ storage_asset('theme/js/deznav-init.js') }}"></script>
    <script src="{{ storage_asset('theme/js/jquery.validity.js') }}"></script>

    <script type="text/javascript">
        $(function() {
            $('.validity').validity()
                .on('submit', function(e) {
                    var $this = $(this),
                        $btn = $this.find('[type="submit"]');
                        $btn.button('loading');
                    if (!$this.valid()) {
                        e.preventDefault();
                        $btn.button('reset');
                    }
            });
        });
        $(".select2").select2({});
    </script>

</body>

</html>