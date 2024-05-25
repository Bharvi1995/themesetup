<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>API Form</title>
        <link href="{{ storage_asset('NewTheme/assets/lib/@fortawesome/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
        <link href="{{ storage_asset('NewTheme/assets/lib/ionicons/css/ionicons.min.css') }}" rel="stylesheet">
        <link href="{{ storage_asset('NewTheme/assets/lib/select2/css/select2.min.css') }}" rel="stylesheet">
        <link rel="stylesheet" href="{{ storage_asset('NewTheme/assets/css/dashforge.css') }}">
        <link rel="stylesheet" href="{{ storage_asset('NewTheme/assets/css/dashforge.demo.css') }}">
        <style type="text/css">
            footer ul {
                margin: 0px;
            }
            footer li {
                float: left;
                list-style: none;
                height: 60px;
                position: relative;
                width: 80px;
            }
            footer li img {
                position: absolute;
                left: 50%;
                top: 50%;
                transform: translate(-50%, -50%);
            }
            .content-fixed {
                margin-top: 0px;
            }
            .help-block {
                color: red;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="row">

                <div class="col-md-12">
                    @if($message = Session::get('error'))
                        <div class="alert alert-danger alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                            <strong>Error!</strong> {{ $message }}
                        </div>
                    @endif
                    {!! Session::forget('error') !!}
                    @if($message = Session::get('success'))
                        <div class="alert alert-success alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                            <strong>Success!</strong> {{ $message }}
                        </div>
                    @endif
                    {!! Session::forget('success') !!}
                    @if($message = Session::get('warning'))
                        <div class="alert alert-warning alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                            <strong>Warning!</strong> {{ $message }}
                        </div>
                    @endif
                    {!! Session::forget('warning') !!}
                    <form id="submit_form" method="POST" action="{{ route('theteller') }}" style="margin-top: 100px;">
                        {!! csrf_field() !!}
                        <div class="portlet-body form">
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div data-label="Billing Info" class="df-example demo-forms">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group{{ $errors->has('first_name') ? ' has-error' : '' }}">
                                                        <label>First Name</label>
                                                        <input class="form-control spinner" name="first_name" type="text" id="first_name" placeholder="First Name" value="{{ old('first_name') }}">
                                                        @if ($errors->has('first_name'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('first_name') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group{{ $errors->has('last_name') ? ' has-error' : '' }}">
                                                        <label>Last Name</label>
                                                        <input class="form-control spinner" name="last_name" type="text" id="last_name" placeholder="Last Name" value="{{ old('last_name') }}">
                                                        @if ($errors->has('last_name'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('last_name') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">
                                                        <label>Address</label>
                                                        <input class="form-control spinner" name="address" type="text" id="address" placeholder="Address" value="{{ old('address') }}">
                                                        @if ($errors->has('address'))
                                                        <span class="help-block">
                                                            <strong>{{ $errors->first('address') }}</strong>
                                                        </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group{{ $errors->has('country') ? ' has-error' : '' }}">
                                                        <label>Country</label>
                                                        <select class="form-control select2" name="country" id="country">
                                                            <option selected disabled> -- Select Country Type -- </option>
                                                            @foreach(getCountry() as $key => $value)
                                                            <option value="{{ $key }}">{{ $value }}</option>
                                                            @endforeach
                                                        </select>
                                                        @if ($errors->has('country'))
                                                        <span class="help-block">
                                                            <strong>{{ $errors->first('country') }}</strong>
                                                        </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group{{ $errors->has('state') ? ' has-error' : '' }}">
                                                        <label>State</label>
                                                        <input class="form-control" name="state" id="state" placeholder="Please enter 2 letter state code for US, and any string for other countries" value="{{ old('state') }}">
                                                        @if ($errors->has('state'))
                                                        <span class="help-block">
                                                            <strong>{{ $errors->first('state') }}</strong>
                                                        </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group{{ $errors->has('city') ? ' has-error' : '' }}">
                                                        <label>City</label>
                                                        <input class="form-control spinner" name="city" type="text" id="city" placeholder="City" value="{{ old('city') }}">
                                                        @if ($errors->has('city'))
                                                        <span class="help-block">
                                                            <strong>{{ $errors->first('city') }}</strong>
                                                        </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group{{ $errors->has('zip') ? ' has-error' : '' }}">
                                                        <label>Zip Code</label>
                                                        <input class="form-control spinner" name="zip" type="text" id="zip" placeholder="Zip Code" value="{{ old('zip') }}">
                                                        @if ($errors->has('zip'))
                                                        <span class="help-block">
                                                            <strong>{{ $errors->first('zip') }}</strong>
                                                        </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                                        <label>Email</label>
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                                                            </div>
                                                            <input class="form-control spinner" name="email" type="email" id="email" placeholder="Email" value="{{ isset($_GET['email'])?$_GET['email']:'' }}" {{ isset($_GET['email'])?'readonly':'' }}>
                                                        </div>
                                                        @if ($errors->has('email'))
                                                        <span class="help-block">
                                                            <strong>{{ $errors->first('email') }}</strong>
                                                        </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group{{ $errors->has('phone_no') ? ' has-error' : '' }}">
                                                        <label>Phone No.</label>
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="fa fa-mobile-alt"></i></span>
                                                            </div>
                                                            <input class="form-control spinner" name="phone_no" type="number" placeholder="Phone No." id="phone_no" value="{{ old('phone_no') }}">
                                                        </div>
                                                        @if ($errors->has('phone_no'))
                                                        <span class="help-block">
                                                            <strong>{{ $errors->first('phone_no') }}</strong>
                                                        </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div data-label="Card Info" class="df-example demo-forms">
                                            <div class="form-group{{ $errors->has('card_type') ? ' has-error' : '' }}">
                                                <label>Card Type</label>
                                                <select class="form-control select2" name="card_type" id="card_type">
                                                    <option selected disabled> -- Select Card Type -- </option>
                                                    <option value="1">Amex</option>
                                                    <option value="2">Visa</option>
                                                    <option value="3">Mastercard</option>
                                                    <option value="4">Discover</option>
                                                </select>
                                                @if ($errors->has('card_type'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('card_type') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                            <div class="form-group{{ $errors->has('amount') ? ' has-error' : '' }}">
                                                <label>Amount</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-dollar-sign"></i></span>
                                                    </div>
                                                    <input class="form-control spinner" name="amount" type="text" placeholder="Amount" value="{{ old('amount') }}" id="amount">
                                                </div>
                                                @if ($errors->has('amount'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('amount') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                            <div class="form-group {{ $errors->has('currency') ? ' has-error' : '' }}">
                                                <label>Currency</label>
                                                <select class="form-control select2" name="currency" id="currency">
                                                    <option selected disabled> -- Select Currency -- </option>
                                                    <option value="USD">USD</option>
                                                    <option value="GHS">GHS</option>
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
                                                </select>
                                                @if ($errors->has('currency'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('currency') }}</strong>
                                                </span>
                                                @endif
                                            </div>

                                            <div class="form-group {{ $errors->has('card_no') ? ' has-error' : '' }}">
                                                <label>Card No.</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-credit-card"></i></span>
                                                    </div>
                                                    {!! Form::number('card_no', old('card_no'), array('placeholder' => 'Card No.', 'class' => 'form-control', 'id' => 'card')) !!}
                                                </div>
                                                <strong class="text-danger log"></strong>
                                                @if ($errors->has('card_no'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('card_no') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group {{ $errors->has('ccExpiryMonth') ? ' has-error' : '' }}">
                                                        <label>Expiry Month</label>
                                                        <select class="form-control select2" name="ccExpiryMonth" id="ccExpiryMonth">
                                                            <option selected disabled> -- Select Exp. Month -- </option>
                                                            <option value="01">01</option>
                                                            <option value="02">02</option>
                                                            <option value="03">03</option>
                                                            <option value="04">04</option>
                                                            <option value="05">05</option>
                                                            <option value="06">06</option>
                                                            <option value="07">07</option>
                                                            <option value="08">08</option>
                                                            <option value="09">09</option>
                                                            <option value="10">10</option>
                                                            <option value="11">11</option>
                                                            <option value="12">12</option>
                                                        </select>
                                                        @if ($errors->has('ccExpiryMonth'))
                                                        <span class="help-block">
                                                            <strong>{{ $errors->first('ccExpiryMonth') }}</strong>
                                                        </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group {{ $errors->has('ccExpiryYear') ? ' has-error' : '' }}">
                                                        <label>Expiry Year</label>
                                                        <select class="form-control select2" name="ccExpiryYear" id="ccExpiryYear">
                                                            <option selected disabled> -- Select Exp. Year -- </option>
                                                            <option value="2018">2018</option>
                                                            <option value="2019">2019</option>
                                                            <option value="2020">2020</option>
                                                            <option value="2021">2021</option>
                                                            <option value="2022">2022</option>
                                                            <option value="2023">2023</option>
                                                            <option value="2024">2024</option>
                                                            <option value="2025">2025</option>
                                                            <option value="2026">2026</option>
                                                            <option value="2027">2027</option>
                                                            <option value="2028">2028</option>
                                                            <option value="2029">2029</option>
                                                            <option value="2030">2030</option>
                                                            <option value="2031">2031</option>
                                                            <option value="2032">2032</option>
                                                            <option value="2033">2033</option>
                                                            <option value="2034">2034</option>
                                                            <option value="2035">2035</option>
                                                            <option value="2036">2036</option>
                                                            <option value="2037">2037</option>
                                                            <option value="2038">2038</option>
                                                            <option value="2039">2039</option>
                                                            <option value="2040">2040</option>
                                                        </select>
                                                        @if ($errors->has('ccExpiryYear'))
                                                        <span class="help-block">
                                                            <strong>{{ $errors->first('ccExpiryYear') }}</strong>
                                                        </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group {{ $errors->has('cvvNumber') ? ' has-error' : '' }}">
                                                <label>CVV No.</label>
                                                {!! Form::number('cvvNumber', old('cvvNumber'), array('placeholder' => 'CVV No.', 'class' => 'form-control', 'id' => 'cvvNumber')) !!}
                                                @if ($errors->has('cvvNumber'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('cvvNumber') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div  class="clearfix"></div>
                        <hr>
                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-offset-3 col-md-9">
                                    <button type="submit" id="submitForm" class="btn btn-success btn-sm">Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <br>

        <script type="text/javascript" src="{{ storage_asset('themeAdmin/js/jquery-latest.min.js') }}"></script>
        <script src="{{ storage_asset('NewTheme/assets/lib/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ storage_asset('NewTheme/assets/lib/feather-icons/feather.min.js') }}"></script>
        <script src="{{ storage_asset('NewTheme/assets/lib/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
        <script src="{{ storage_asset('NewTheme/assets/lib/select2/js/select2.min.js') }}"></script>
        <script src="{{ storage_asset('NewTheme/assets/js/dashforge.js') }}"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                $(".select2").select2();
            });
        </script>
    </body>
</html>
