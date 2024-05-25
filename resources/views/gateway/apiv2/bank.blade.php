<!DOCTYPE html>
<html class="loading dark-layout" lang="en" data-textdirection="ltr">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <title>{{ config('app.name') }} | {{ __('messages.pageTitleBank') }}</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ storage_asset('NewTheme/images/favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css"
        integrity="sha512-5A8nwdMOWrSz20fDsjczgUidUBR8liPYU+WymTZP1lmY9G6Oc7HlZv156XqnsgNUzTyMefFTcsFH/tnJE/+xBg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('NewTheme/css/bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('NewTheme/css/bootstrap-extended.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('NewTheme/css/auth.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('NewTheme/css/select2.min.css') }}">
    <style type="text/css">
        .card {
            background: var(--white);
            border-radius: 0px 0px 3px 3px;
            box-shadow: 0px 2px 5px 0px #05309533;
        }

        .btn-danger {
            background: var(--primary-1) !important;
            border-color: var(--primary-1) !important;
            color: var(--white) !important;
            border-radius: 3px;
        }

        .btn-primary {
            background: var(--primary-3) !important;
            border-color: var(--primary-3) !important;
            color: var(--primary-4) !important;
            border-radius: 3px;
        }

        .select2-dropdown {
            background-color: #494949 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #B9B9C3 !important;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #F44336 !important;
        }
    </style>
</head>

<body>
    <div id="loading">
        <p class="mt-1">{{ __('messages.loading') }}...</p>
    </div>
    <div class="app-content content">
        <div class="container">
            <div class="row content-body">
                <div class="col-md-4 col-xl-4 col-xxl-4">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card mt-2">
                                <div class="card-body gateway-card">
                                    <div class="row">
                                        <div class="col-md-12 text-center">
                                            <p class="text-danger">{{ __('messages.payWithBank') }}</p>
                                            <h3 class="text-primary">
                                                {{ $input['amount'] }} {{ $input['currency'] }}
                                            </h3>
                                            <p>{{ $input['email'] }}</p>
                                        </div>

                                        <div class="col-md-12">
                                            <form action="{{ route('api.v2.bankSubmit', $order_id) }}"
                                                id="extra-details-form" method="post">
                                                @csrf
                                                <div class="auth-form">
                                                    <div class="row">
                                                        <div class="col-sm-12 col-md-12">
                                                            <div class="row">
                                                                <h4 class="text-primary">
                                                                    {{ __('messages.paymentDetails') }}</h4>
                                                                @if (in_array('phone_no', $data))
                                                                    <div class="col-md-12 form-group mb-1">
                                                                        <label>{{ __('messages.phoneNo') }}</label>
                                                                        <input class="form-control required-field"
                                                                            name="phone_no" type="text"
                                                                            id="phone_no" placeholder="Ex. 9898098980"
                                                                            minlength="2" maxlength="50"
                                                                            value="{{ $input['phone_no'] ?? null }}"
                                                                            required
                                                                            data-missing="Phone number field is required">
                                                                        @if ($errors->has('phone_no'))
                                                                            <span class="help-block">
                                                                                <strong>{{ $errors->first('phone_no') }}</strong>
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                @endif
                                                                @if (in_array('zip', $data))
                                                                    <div class="col-md-12 form-group mb-1">
                                                                        <label>{{ __('messages.zipCode') }}</label>
                                                                        <input class="form-control required-field"
                                                                            name="zip" type="text" id="zip"
                                                                            placeholder="380060"
                                                                            value="{{ $input['zip'] ?? null }}"
                                                                            required
                                                                            data-missing="ZIP field is required">
                                                                        @if ($errors->has('zip'))
                                                                            <span class="help-block">
                                                                                <strong>{{ $errors->first('zip') }}</strong>
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                @endif
                                                                @if (in_array('address', $data))
                                                                    <div class="col-md-12 form-group mb-1">
                                                                        <label>{{ __('messages.address') }}</label>
                                                                        <input class="form-control required-field"
                                                                            name="address" type="text" id="address"
                                                                            placeholder="203/C, Alice Hills"
                                                                            minlength="2" maxlength="50"
                                                                            value="{{ $input['address'] ?? null }}"
                                                                            required
                                                                            data-missing="Address field is required">
                                                                        @if ($errors->has('address'))
                                                                            <span class="help-block">
                                                                                <strong>{{ $errors->first('address') }}</strong>
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                @endif
                                                                @if (in_array('country', $data))
                                                                    <div class="col-md-12 form-group mb-1">
                                                                        <label>{{ __('messages.country') }}</label>
                                                                        <div><select
                                                                                class="form-control select2 required-field"
                                                                                name="country" id="country11" required
                                                                                data-missing="Country field is required">
                                                                                <option value="IN"
                                                                                    {{ isset($input['country']) && $input['country'] == 'IN' ? 'selected' : null }}>
                                                                                    India</option>
                                                                                @foreach (getCountry() as $key => $value)
                                                                                    <option value="{{ $key }}"
                                                                                        {{ isset($input['country']) && $input['country'] == $key ? 'selected' : null }}>
                                                                                        {{ $value }}</option>
                                                                                @endforeach
                                                                            </select></div>
                                                                        @if ($errors->has('country'))
                                                                            <span class="help-block">
                                                                                <strong>{{ $errors->first('country') }}</strong>
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                @endif
                                                                @if (in_array('city', $data))
                                                                    <div class="col-md-12 form-group mb-1">
                                                                        <label>{{ __('messages.city') }}</label>
                                                                        <input class="form-control required-field"
                                                                            name="city" type="text"
                                                                            id="city" placeholder="Amadavad"
                                                                            minlength="2" maxlength="50"
                                                                            value="{{ $input['city'] ?? null }}"
                                                                            required
                                                                            data-missing="City field is required">
                                                                        @if ($errors->has('city'))
                                                                            <span class="help-block">
                                                                                <strong>{{ $errors->first('city') }}</strong>
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                @endif
                                                                @if (in_array('state', $data))
                                                                    <div class="col-md-12 form-group mb-1">
                                                                        <label>{{ __('messages.state') }}</label>
                                                                        <input class="form-control required-field"
                                                                            name="state" type="text"
                                                                            id="state" placeholder="Gujarat"
                                                                            minlength="2" maxlength="50"
                                                                            value="{{ $input['state'] ?? null }}"
                                                                            required
                                                                            data-missing="State field is required">
                                                                        @if ($errors->has('state'))
                                                                            <span class="help-block">
                                                                                <strong>{{ $errors->first('state') }}</strong>
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="pd-25">
                                                    <div class="common-btns fl-btns">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <button type="button" id="submit-button"
                                                                    class="btn btn-danger w-100">{{ __('messages.payNow') }}</button>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <button type="button" id="cancel-button"
                                                                    class="btn btn-primary w-100">{{ __('messages.cancel') }}</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.js'></script>
    <script src="{{ storage_asset('NewTheme/vendors/js/forms/select/select2.full.min.js') }}"></script>
    <script>
        jQuery(document).ready(function() {
            jQuery("#load").fadeOut();
            jQuery("#loading").delay().fadeOut("");
            $(".select2").select2({})
        });
    </script>
    <script type="text/javascript">
        // submit form
        $(document).on('click', '#submit-button', function(e) {
            e.preventDefault();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var data = $('#extra-details-form').serialize();
            $.ajax({
                url: '{{ route('api.v2.bankSubmit', $order_id) }}',
                type: 'post',
                data: data,
                beforeSend: function() {
                    $('#validation-errors').html('');
                    showLoader();
                },
                success: function(data) {
                    if (data.status == 'success') {
                        window.location = data.url;
                    } else {
                        $('#validation-errors').append('<div class="alert alert-danger">' + data
                            .message + '</div');
                    }
                },
                fail: function(err) {
                    $('#validation-errors').append('<div class="alert alert-danger">' + data.message +
                        '</div');
                },
                error: function(jqXHR, exception) {
                    $('#validation-errors').append(
                        '<div class="alert alert-danger">something went wrong, please try again</div'
                    );
                }
            }).always(function(jqXHR, exception) {
                hideLoader();
            });
        });
        $(document).on('click', '#cancel-button', function(e) {
            window.location = "{{ route('api.v2.decline', $order_id) }}";
        });

        function showLoader() {
            jQuery("#load").fadeIn();
            jQuery("#loading").delay().fadeIn();
        }

        function hideLoader() {
            jQuery("#load").fadeOut();
            jQuery("#loading").delay().fadeOut();
        }
    </script>
</body>

</html>
