@php
    $currency = getCurrency();
@endphp
<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <title>{{ config('app.name') }} | {{ __('messages.pageTitleLink') }}</title>
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
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('NewTheme/css/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('NewTheme/css/auth.css') }}">
    <style type="text/css">
        .auth-form {
            padding: 30px;
            border-radius: 3px;
        }

        .error {
            color: #ea5455 !important;
        }

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

        .langDropdown {
            background: #ffffff !important;
        }

        .langDropdown li:hover {
            background-color: var(--primary-1) !important;

        }

        .langDropdown .dropdown-item:hover {
            color: #ffffff !important;
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
                <div class="col-md-5 col-xl-5 col-xxl-5">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            @if ($userData->iframe_logo != '' && $userData != null)
                                <img src="{{ getS3Url($userData->iframe_logo) }}" style="max-width: 250px;">
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card mt-2">
                                <div class="card-body gateway-card">
                                    {{-- response messages --}}
                                    @if (\Session::has('error'))
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <div class="alert-body">
                                                {{ \Session::get('error') }}
                                            </div>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                        </div>
                                        {{ \Session::forget('error') }}
                                    @endif

                                    @if (\Session::has('pending'))
                                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                            <div class="alert-body">
                                                {{ \Session::get('pending') }}
                                            </div>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                        </div>
                                        {{ \Session::forget('pending') }}
                                    @endif

                                    @if (\Session::has('success'))
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            <div class="alert-body">
                                                {{ \Session::get('success') }}
                                            </div>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                        </div>
                                        {{ \Session::forget('success') }}
                                    @endif

                                    <form method="POST" action="{{ route('iframe2.checkout-form', $token) }}"
                                        accept-charset="UTF-8"
                                        onsubmit="document.getElementById('disableBTN').disabled=true; document.getElementById('disableBTN')"
                                        class="validity form-dark" enctype="multipart/form-data" novalidate="true">
                                        <input type="hidden" name="api_key" value="{{ $userData->api_key }}">
                                        <div class="d-flex  justify-content-end ">
                                            @include('partials.payment.languageBtn')
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 form-group mb-1">
                                                <label class="mb-25">{{ __('messages.firstName') }} <span
                                                        class="text-danger">*</span></label>
                                                <div>
                                                    <input type="text" name="first_name" class="form-control"
                                                        id="first_name" placeholder="{{ __('messages.enterHere') }}"
                                                        value="{{ isset($_GET['first_name']) ? $_GET['first_name'] : '' }}"
                                                        {{ isset($_GET['first_name']) ? 'readonly' : '' }} required
                                                        data-missing="This field is required">
                                                </div>
                                                @if ($errors->has('first_name'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('first_name') }}</strong>
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="col-md-6 form-group mb-1">
                                                <label class="mb-25">{{ __('messages.lastName') }} <span
                                                        class="text-danger">*</span></label>
                                                <div>
                                                    <input type="text" name="last_name" class="form-control"
                                                        id="last_name" placeholder="{{ __('messages.enterHere') }}"
                                                        value="{{ isset($_GET['last_name']) ? $_GET['last_name'] : '' }}"
                                                        {{ isset($_GET['last_name']) ? 'readonly' : '' }} required
                                                        data-missing="This field is required">
                                                </div>
                                                @if ($errors->has('first_name'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('first_name') }}</strong>
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="col-md-12 form-group mb-1">
                                                <label class="mb-25">{{ __('messages.email') }} <span
                                                        class="text-danger">*</span></label>
                                                <div>
                                                    <input type="email" name="email" class="form-control"
                                                        id="email" placeholder="{{ __('messages.enterHere') }}"
                                                        value="{{ isset($_GET['email']) ? $_GET['email'] : '' }}"
                                                        {{ isset($_GET['email']) ? 'readonly' : '' }} required
                                                        data-missing="This field is required">
                                                </div>
                                                @if ($errors->has('email'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('email') }}</strong>
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="col-md-6 form-group mb-1">
                                                <label class="mb-25">{{ __('messages.currency') }} <span
                                                        class="text-danger">*</span></label>
                                                <div>
                                                    <select class="form-control select2" name="currency"
                                                        id="currency" required data-missing="This field is required">
                                                        @if (isset($iframe_array['currency']) && in_array($iframe_array['currency'], $currency))
                                                            <option value="{{ $iframe_array['currency'] }}">
                                                                {{ $iframe_array['currency'] }}</option>
                                                        @else
                                                            <option value="" selected disabled>Select</option>
                                                            @foreach (getCurrency() as $key => $value)
                                                                <option value="{{ $key }}">
                                                                    {{ $value }}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                                @if ($errors->has('currency'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('currency') }}</strong>
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="col-md-6 form-group mb-1">
                                                <label class="mb-25">{{ __('messages.amount') }} <span
                                                        class="text-danger">*</span></label>
                                                <div>
                                                    <input class="form-control" name="amount" type="text"
                                                        placeholder="{{ __('messages.enterHere') }}" id="amount"
                                                        value="{{ isset($iframe_array['amount']) ? $iframe_array['amount'] : '' }}"
                                                        {{ isset($iframe_array['amount']) ? 'readonly' : '' }} required
                                                        data-missing="This field is required">
                                                </div>
                                                @if ($errors->has('amount'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('amount') }}</strong>
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="col-md-12 mt-1">
                                                <button type="submit"
                                                    class="btn btn-danger w-100">{{ __('messages.payNow') }}</button>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.js'></script>
    <script src="{{ storage_asset('themeAdmin/js/select2.min.js') }}"></script>
    <script src="{{ storage_asset('NewTheme/js/jquery.validity.js') }}"></script>
    <script>
        var url = "{{ route('change.lang') }}"
        jQuery(document).ready(function() {
            jQuery("#load").fadeOut();
            jQuery("#loading").delay().fadeOut("");

            $(document).on("click", ".langListBtn", function() {
                var val = $(this).attr("data-lang");
                window.location.href = url + "?lang=" + val;
            });
        });
    </script>
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
        $(".select2").select2();
    </script>
</body>

</html>
