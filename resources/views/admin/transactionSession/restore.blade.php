@extends('layouts.admin.default')

@section('style')
    <link href="{{ asset('NewTheme/assets/lib/select2/css/select2.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('NewTheme/assets/css/dashforge.css') }}">
    <link rel="stylesheet" href="{{ asset('NewTheme/assets/css/dashforge.demo.css') }}">
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="header-title">
                        <h4 class="card-title">Transaction Session Details</h4>
                    </div>
                    <div class="btn-group">
                        <a href="{{ route('transaction-session') }}" class="btn btn-primary btn-sm"><i
                                class="fa fa-arrow-left"></i> </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">
                            @if ($message = Session::get('error'))
                                <div class="alert alert-danger alert-dismissible" role="alert">
                                    <button type="button" class="btn-close" data-bs-dismiss="alert">×</button>
                                    <div class="alert-message">
                                        <span><strong>Error!</strong> {{ $message }}</span>
                                    </div>
                                </div>
                            @endif
                            {!! Session::forget('error') !!}
                            @if ($message = Session::get('success'))
                                <div class="alert alert-success alert-dismissible" role="alert">
                                    <button type="button" class="btn-close" data-bs-dismiss="alert">×</button>
                                    <div class="alert-message">
                                        <span><strong>Success!</strong> {{ $message }}</span>
                                    </div>
                                </div>
                            @endif
                            {!! Session::forget('success') !!}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div data-label="Restore Transaction" class="df-example demo-table">
                                <form
                                    action="{{ route('admin.restoreTransactionSession', $transaction_session['session_id']) }}"
                                    method="post" class="form-dark">
                                    <input type="hidden" name="session_id"
                                        value="{{ $transaction_session['session_id'] }}">
                                    @csrf
                                    <div class="row">

                                        <div class="col-sm-6">
                                            <div class="form-group row {{ $errors->has('user_id') ? ' has-error' : '' }}">
                                                <label class="col-sm-4 col-form-label">Select Merchant</label>
                                                <div class="col-sm-8">
                                                    <select name="user_id" class="form-control">
                                                        <option>-- Select MID --</option>
                                                        @foreach ($companyName as $value)
                                                            <option value="{{ $value->user_id }}"
                                                                {{ $value->user_id == $transaction_session['user_id'] ? 'selected' : '' }}>
                                                                {{ $value->business_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @if ($errors->has('user_id'))
                                                        <span class="help-block">
                                                            <strong
                                                                class="text-danger">{{ $errors->first('user_id') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div
                                                class="form-group row {{ $errors->has('payment_gateway_id') ? ' has-error' : '' }}">
                                                <label class="col-sm-4 col-form-label">Select MID</label>
                                                <div class="col-sm-8">
                                                    <select name="payment_gateway_id" class="form-control">
                                                        <option>-- Select MID --</option>
                                                        @foreach ($payment_gateway_id as $key => $value)
                                                            <option value="{{ $key }}"
                                                                {{ $key == $transaction_session['payment_gateway_id'] ? 'selected' : '' }}>
                                                                {{ $value }}</option>
                                                        @endforeach
                                                    </select>
                                                    @if ($errors->has('payment_gateway_id'))
                                                        <span class="help-block">
                                                            <strong
                                                                class="text-danger">{{ $errors->first('payment_gateway_id') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        @if (array_key_exists('first_name', $transaction_session))
                                            <div class="col-sm-6">
                                                <div
                                                    class="form-group row {{ $errors->has('first_name') ? ' has-error' : '' }}">
                                                    <label class="col-sm-4 col-form-label">First Name</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="first_name"
                                                            value="{{ old('first_name') ?? $transaction_session['first_name'] }}"
                                                            class="form-control">
                                                        @if ($errors->has('first_name'))
                                                            <span class="help-block">
                                                                <strong
                                                                    class="text-danger">{{ $errors->first('first_name') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if (array_key_exists('last_name', $transaction_session))
                                            <div class="col-sm-6">
                                                <div
                                                    class="form-group row {{ $errors->has('last_name') ? ' has-error' : '' }}">
                                                    <label class="col-sm-4 col-form-label">Last Name</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="last_name"
                                                            value="{{ old('last_name') ?? $transaction_session['last_name'] }}"
                                                            class="form-control">
                                                        @if ($errors->has('last_name'))
                                                            <span class="help-block">
                                                                <strong
                                                                    class="text-danger">{{ $errors->first('last_name') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if (array_key_exists('address', $transaction_session))
                                            <div class="col-sm-6">
                                                <div
                                                    class="form-group row {{ $errors->has('address') ? ' has-error' : '' }}">
                                                    <label class="col-sm-4 col-form-label">Address</label>
                                                    <div class="col-sm-8">
                                                        <textarea name="address" class="form-control" style="height: 100px;">{{ old('address') ?? $transaction_session['address'] }}</textarea>
                                                        @if ($errors->has('address'))
                                                            <span class="help-block">
                                                                <strong
                                                                    class="text-danger">{{ $errors->first('address') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if (array_key_exists('country', $transaction_session))
                                            <div class="col-sm-6">
                                                <div
                                                    class="form-group row {{ $errors->has('country') ? ' has-error' : '' }}">
                                                    <label class="col-sm-4 col-form-label">Select Country</label>
                                                    <div class="col-sm-8">
                                                        <select name="country" class="form-control select2">
                                                            <option>-- Select Country --</option>
                                                            @foreach (getCountry() as $key => $value)
                                                                <option value="{{ $key }}"
                                                                    {{ $transaction_session['country'] == $key ? 'selected' : '' }}>
                                                                    {{ $value }}</option>
                                                            @endforeach
                                                        </select>
                                                        @if ($errors->has('country'))
                                                            <span class="help-block">
                                                                <strong
                                                                    class="text-danger">{{ $errors->first('country') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if (array_key_exists('state', $transaction_session))
                                            <div class="col-sm-6">
                                                <div
                                                    class="form-group row {{ $errors->has('state') ? ' has-error' : '' }}">
                                                    <label class="col-sm-4 col-form-label">State</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="state"
                                                            value="{{ old('state') ?? $transaction_session['state'] }}"
                                                            class="form-control">
                                                        @if ($errors->has('state'))
                                                            <span class="help-block">
                                                                <strong
                                                                    class="text-danger">{{ $errors->first('state') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if (array_key_exists('city', $transaction_session))
                                            <div class="col-sm-6">
                                                <div
                                                    class="form-group row {{ $errors->has('city') ? ' has-error' : '' }}">
                                                    <label class="col-sm-4 col-form-label">City</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="city"
                                                            value="{{ old('city') ?? $transaction_session['city'] }}"
                                                            class="form-control">
                                                        @if ($errors->has('city'))
                                                            <span class="help-block">
                                                                <strong
                                                                    class="text-danger">{{ $errors->first('city') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if (array_key_exists('zip', $transaction_session))
                                            <div class="col-sm-6">
                                                <div class="form-group row {{ $errors->has('zip') ? ' has-error' : '' }}">
                                                    <label class="col-sm-4 col-form-label">Zip</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="zip"
                                                            value="{{ old('zip') ?? $transaction_session['zip'] }}"
                                                            class="form-control">
                                                        @if ($errors->has('zip'))
                                                            <span class="help-block">
                                                                <strong
                                                                    class="text-danger">{{ $errors->first('zip') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if (array_key_exists('email', $transaction_session))
                                            <div class="col-sm-6">
                                                <div
                                                    class="form-group row {{ $errors->has('email') ? ' has-error' : '' }}">
                                                    <label class="col-sm-4 col-form-label">Email</label>
                                                    <div class="col-sm-8">
                                                        <input type="email" name="email"
                                                            value="{{ old('email') ?? $transaction_session['email'] }}"
                                                            class="form-control">
                                                        @if ($errors->has('email'))
                                                            <span class="help-block">
                                                                <strong
                                                                    class="text-danger">{{ $errors->first('email') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if (array_key_exists('phone_no', $transaction_session))
                                            <div class="col-sm-6">
                                                <div
                                                    class="form-group row {{ $errors->has('phone_no') ? ' has-error' : '' }}">
                                                    <label class="col-sm-4 col-form-label">Phone No</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="phone_no"
                                                            value="{{ old('phone_no') ?? $transaction_session['phone_no'] }}"
                                                            class="form-control">
                                                        @if ($errors->has('phone_no'))
                                                            <span class="help-block">
                                                                <strong
                                                                    class="text-danger">{{ $errors->first('phone_no') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if (array_key_exists('card_type', $transaction_session))
                                            <div class="col-sm-6">
                                                <div
                                                    class="form-group row {{ $errors->has('card_type') ? ' has-error' : '' }}">
                                                    <label class="col-sm-4 col-form-label">Select Card Type</label>
                                                    <div class="col-sm-8">
                                                        <select class="form-control single-select" name="card_type">
                                                            <option selected disabled> -- Select Card Type -- </option>
                                                            <option value="1"
                                                                {{ $transaction_session['card_type'] == '1' ? 'selected' : '' }}>
                                                                Amex</option>
                                                            <option value="2"
                                                                {{ $transaction_session['card_type'] == '2' ? 'selected' : '' }}>
                                                                Visa</option>
                                                            <option value="3"
                                                                {{ $transaction_session['card_type'] == '3' ? 'selected' : '' }}>
                                                                Mastercard</option>
                                                            <option value="4"
                                                                {{ $transaction_session['card_type'] == '4' ? 'selected' : '' }}>
                                                                Discover</option>
                                                        </select>
                                                        @if ($errors->has('card_type'))
                                                            <span class="help-block">
                                                                <strong
                                                                    class="text-danger">{{ $errors->first('card_type') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if (array_key_exists('amount', $transaction_session))
                                            <div class="col-sm-6">
                                                <div
                                                    class="form-group row {{ $errors->has('amount') ? ' has-error' : '' }}">
                                                    <label class="col-sm-4 col-form-label">Amount</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="amount"
                                                            value="{{ old('amount') ?? $transaction_session['amount'] }}"
                                                            class="form-control">
                                                        @if ($errors->has('amount'))
                                                            <span class="help-block">
                                                                <strong
                                                                    class="text-danger">{{ $errors->first('amount') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if (array_key_exists('currency', $transaction_session))
                                            <div class="col-sm-6">
                                                <div
                                                    class="form-group row {{ $errors->has('currency') ? ' has-error' : '' }}">
                                                    <label class="col-sm-4 col-form-label">Select Currency</label>
                                                    <div class="col-sm-8">
                                                        <select class="form-control" name="currency">
                                                            <option selected disabled> -- Select Currency -- </option>
                                                            <option value="USD"
                                                                {{ $transaction_session['currency'] == 'USD' ? 'selected' : '' }}>
                                                                USD</option>
                                                            <option value="HKD"
                                                                {{ $transaction_session['currency'] == 'HKD' ? 'selected' : '' }}>
                                                                HKD</option>
                                                            <option value="GBP"
                                                                {{ $transaction_session['currency'] == 'GBP' ? 'selected' : '' }}>
                                                                GBP</option>
                                                            <option value="CNY"
                                                                {{ $transaction_session['currency'] == 'CNY' ? 'selected' : '' }}>
                                                                CNY</option>
                                                            <option value="JPY"
                                                                {{ $transaction_session['currency'] == 'JPY' ? 'selected' : '' }}>
                                                                JPY</option>
                                                            <option value="EUR"
                                                                {{ $transaction_session['currency'] == 'EUR' ? 'selected' : '' }}>
                                                                EUR</option>
                                                            <option value="AUD"
                                                                {{ $transaction_session['currency'] == 'AUD' ? 'selected' : '' }}>
                                                                AUD</option>
                                                            <option value="CAD"
                                                                {{ $transaction_session['currency'] == 'CAD' ? 'selected' : '' }}>
                                                                CAD</option>
                                                            <option value="SGD"
                                                                {{ $transaction_session['currency'] == 'SGD' ? 'selected' : '' }}>
                                                                SGD</option>
                                                            <option value="NZD"
                                                                {{ $transaction_session['currency'] == 'NZD' ? 'selected' : '' }}>
                                                                NZD</option>
                                                            <option value="TWD"
                                                                {{ $transaction_session['currency'] == 'TWD' ? 'selected' : '' }}>
                                                                TWD</option>
                                                            <option value="KRW"
                                                                {{ $transaction_session['currency'] == 'KRW' ? 'selected' : '' }}>
                                                                KRW</option>
                                                            <option value="DKK"
                                                                {{ $transaction_session['currency'] == 'DKK' ? 'selected' : '' }}>
                                                                DKK</option>
                                                            <option value="TRL"
                                                                {{ $transaction_session['currency'] == 'TRL' ? 'selected' : '' }}>
                                                                TRL</option>
                                                            <option value="MYR"
                                                                {{ $transaction_session['currency'] == 'MYR' ? 'selected' : '' }}>
                                                                MYR</option>
                                                            <option value="THB"
                                                                {{ $transaction_session['currency'] == 'THB' ? 'selected' : '' }}>
                                                                THB</option>
                                                            <option value="INR"
                                                                {{ $transaction_session['currency'] == 'INR' ? 'selected' : '' }}>
                                                                INR</option>
                                                            <option value="PHP"
                                                                {{ $transaction_session['currency'] == 'PHP' ? 'selected' : '' }}>
                                                                PHP</option>
                                                            <option value="CHF"
                                                                {{ $transaction_session['currency'] == 'CHF' ? 'selected' : '' }}>
                                                                CHF</option>
                                                            <option value="SEK"
                                                                {{ $transaction_session['currency'] == 'SEK' ? 'selected' : '' }}>
                                                                SEK</option>
                                                            <option value="ILS"
                                                                {{ $transaction_session['currency'] == 'ILS' ? 'selected' : '' }}>
                                                                ILS</option>
                                                            <option value="ZAR"
                                                                {{ $transaction_session['currency'] == 'ZAR' ? 'selected' : '' }}>
                                                                ZAR</option>
                                                            <option value="RUB"
                                                                {{ $transaction_session['currency'] == 'RUB' ? 'selected' : '' }}>
                                                                RUB</option>
                                                            <option value="NOK"
                                                                {{ $transaction_session['currency'] == 'NOK' ? 'selected' : '' }}>
                                                                NOK</option>
                                                            <option value="AED"
                                                                {{ $transaction_session['currency'] == 'AED' ? 'selected' : '' }}>
                                                                AED</option>
                                                            <option value="UGX"
                                                                {{ $transaction_session['currency'] == 'UGX' ? 'selected' : '' }}>
                                                                UGX</option>
                                                            <option value="MXN"
                                                                {{ $transaction_session['currency'] == 'MXN' ? 'selected' : '' }}>
                                                                MXN</option>
                                                        </select>
                                                        @if ($errors->has('currency'))
                                                            <span class="help-block">
                                                                <strong
                                                                    class="text-danger">{{ $errors->first('currency') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if (array_key_exists('card_no', $transaction_session))
                                            <div class="col-sm-6">
                                                <div
                                                    class="form-group row {{ $errors->has('card_no') ? ' has-error' : '' }}">
                                                    <label class="col-sm-4 col-form-label">Card No</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="card_no"
                                                            value="{{ old('card_no') ?? $transaction_session['card_no'] }}"
                                                            class="form-control">
                                                        @if ($errors->has('card_no'))
                                                            <span class="help-block">
                                                                <strong
                                                                    class="text-danger">{{ $errors->first('card_no') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if (array_key_exists('ccExpiryMonth', $transaction_session))
                                            <div class="col-sm-6">
                                                <div
                                                    class="form-group row {{ $errors->has('ccExpiryMonth') ? ' has-error' : '' }}">
                                                    <label class="col-sm-4 col-form-label">cc Expiry Month</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="ccExpiryMonth"
                                                            value="{{ old('ccExpiryMonth') ?? $transaction_session['ccExpiryMonth'] }}"
                                                            class="form-control">
                                                        @if ($errors->has('ccExpiryMonth'))
                                                            <span class="help-block">
                                                                <strong
                                                                    class="text-danger">{{ $errors->first('ccExpiryMonth') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if (array_key_exists('ccExpiryYear', $transaction_session))
                                            <div class="col-sm-6">
                                                <div
                                                    class="form-group row {{ $errors->has('ccExpiryYear') ? ' has-error' : '' }}">
                                                    <label class="col-sm-4 col-form-label">cc Expiry Year</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="ccExpiryYear"
                                                            value="{{ old('ccExpiryYear') ?? $transaction_session['ccExpiryYear'] }}"
                                                            class="form-control">
                                                        @if ($errors->has('ccExpiryYear'))
                                                            <span class="help-block">
                                                                <strong
                                                                    class="text-danger">{{ $errors->first('ccExpiryYear') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if (array_key_exists('cvvNumber', $transaction_session))
                                            <div class="col-sm-6">
                                                <div
                                                    class="form-group row {{ $errors->has('cvvNumber') ? ' has-error' : '' }}">
                                                    <label class="col-sm-4 col-form-label">cvv Number</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="cvvNumber"
                                                            value="{{ old('cvvNumber') ?? $transaction_session['cvvNumber'] }}"
                                                            class="form-control">
                                                        @if ($errors->has('cvvNumber'))
                                                            <span class="help-block">
                                                                <strong
                                                                    class="text-danger">{{ $errors->first('cvvNumber') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if (array_key_exists('ip_address', $transaction_session))
                                            <div class="col-sm-6">
                                                <div
                                                    class="form-group row {{ $errors->has('ip_address') ? ' has-error' : '' }}">
                                                    <label class="col-sm-4 col-form-label">IP Address</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="ip_address"
                                                            value="{{ old('ip_address') ?? $transaction_session['ip_address'] }}"
                                                            class="form-control">
                                                        @if ($errors->has('ip_address'))
                                                            <span class="help-block">
                                                                <strong
                                                                    class="text-danger">{{ $errors->first('ip_address') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if (array_key_exists('customer_order_id', $transaction_session))
                                            <div class="col-sm-6">
                                                <div
                                                    class="form-group row {{ $errors->has('customer_order_id') ? ' has-error' : '' }}">
                                                    <label class="col-sm-4 col-form-label">Sulte apt no</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="customer_order_id"
                                                            value="{{ old('customer_order_id') ?? $transaction_session['customer_order_id'] }}"
                                                            class="form-control">
                                                        @if ($errors->has('customer_order_id'))
                                                            <span class="help-block">
                                                                <strong
                                                                    class="text-danger">{{ $errors->first('customer_order_id') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if (array_key_exists('descriptor', $transaction_session))
                                            <div class="col-sm-6">
                                                <div
                                                    class="form-group row {{ $errors->has('descriptor') ? ' has-error' : '' }}">
                                                    <label class="col-sm-4 col-form-label">Descriptor</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="descriptor"
                                                            value="{{ old('descriptor') ?? $transaction_session['descriptor'] }}"
                                                            class="form-control">
                                                        @if ($errors->has('descriptor'))
                                                            <span class="help-block">
                                                                <strong
                                                                    class="text-danger">{{ $errors->first('descriptor') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if (array_key_exists('webhook_url', $transaction_session))
                                            <div class="col-sm-6">
                                                <div
                                                    class="form-group row {{ $errors->has('webhook_url') ? ' has-error' : '' }}">
                                                    <label class="col-sm-4 col-form-label">Webhook url</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="webhook_url"
                                                            value="{{ old('webhook_url') ?? $transaction_session['webhook_url'] }}"
                                                            class="form-control">
                                                        @if ($errors->has('webhook_url'))
                                                            <span class="help-block">
                                                                <strong
                                                                    class="text-danger">{{ $errors->first('webhook_url') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if (array_key_exists('response_url', $transaction_session))
                                            <div class="col-sm-6">
                                                <div
                                                    class="form-group row {{ $errors->has('response_url') ? ' has-error' : '' }}">
                                                    <label class="col-sm-4 col-form-label">Response url</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="response_url"
                                                            value="{{ old('response_url') ?? $transaction_session['response_url'] }}"
                                                            class="form-control">
                                                        @if ($errors->has('response_url'))
                                                            <span class="help-block">
                                                                <strong
                                                                    class="text-danger">{{ $errors->first('response_url') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="col-sm-6">
                                            <div class="form-group row {{ $errors->has('status') ? ' has-error' : '' }}">
                                                <label class="col-sm-4 col-form-label">Status</label>
                                                <div class="col-sm-8">
                                                    <select name="status" class="form-control">
                                                        <option disabled selected> -- Select Status -- </option>
                                                        <option value="0">Declined</option>
                                                        <option value="1">Success</option>
                                                        <option value="2">Pending</option>
                                                        <option value="3">Canceled</option>
                                                        <option value="4">To Be Confirm</option>
                                                    </select>
                                                    @if ($errors->has('status'))
                                                        <span class="help-block">
                                                            <strong
                                                                class="text-danger">{{ $errors->first('status') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group row {{ $errors->has('reason') ? ' has-error' : '' }}">
                                                <label class="col-sm-4 col-form-label">Reason</label>
                                                <div class="col-sm-8">
                                                    {!! Form::text('reason', old('reason'), ['placeholder' => 'Reason', 'class' => 'form-control']) !!}
                                                    @if ($errors->has('reason'))
                                                        <span class="help-block">
                                                            <strong
                                                                class="text-danger">{{ $errors->first('reason') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div
                                                class="form-group row {{ $errors->has('order_id') ? ' has-error' : '' }}">
                                                <label class="col-sm-4 col-form-label">Order No.</label>
                                                <div class="col-sm-8">
                                                    <input disabled type="text" name="order_id"
                                                        value="{{ old('order_id') ?? $transaction_session['order_id'] }}"
                                                        class="form-control">
                                                    @if ($errors->has('order_id'))
                                                        <span class="help-block">
                                                            <strong
                                                                class="text-danger">{{ $errors->first('order_id') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div
                                                class="form-group row {{ $errors->has('created_at') ? ' has-error' : '' }}">
                                                <label class="col-sm-4 col-form-label">Created At</label>
                                                <div class="col-sm-8">
                                                    <input type="text" name="created_at"
                                                        value="{{ old('created_at') ?? date('Y-m-d H:i:s') }}"
                                                        class="form-control">
                                                    @if ($errors->has('created_at'))
                                                        <span class="help-block">
                                                            <strong
                                                                class="text-danger">{{ $errors->first('created_at') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div
                                                class="form-group row {{ $errors->has('updated_at') ? ' has-error' : '' }}">
                                                <label class="col-sm-4 col-form-label">Updated At</label>
                                                <div class="col-sm-8">
                                                    <input type="text" name="updated_at"
                                                        value="{{ old('created_at') ?? date('Y-m-d H:i:s') }}"
                                                        class="form-control">
                                                    @if ($errors->has('updated_at'))
                                                        <span class="help-block">
                                                            <strong
                                                                class="text-danger">{{ $errors->first('updated_at') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="gateway_id"
                                            value="{{ $transaction_session['gateway_id'] }}">
                                    </div>
                                    <button type="submit" class="btn btn-success mt-1">Submit</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('NewTheme/assets/lib/jqueryui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('NewTheme/assets/lib/select2/js/select2.min.js') }}"></script>
    <script type="text/javascript">
        $('select[name="user_id"]').select2();
        $('select[name="country"]').select2();
        $('select[name="payment_gateway_id"]').select2();
        $('select[name="card_type"]').select2();
        $('select[name="currency"]').select2();
        $('select[name="status"]').select2();
    </script>
@endsection
