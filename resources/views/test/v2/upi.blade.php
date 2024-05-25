<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ config('app.name') }} | Test UPI</title>
    <link rel="icon" type="image/png" sizes="16x16" href="{{ storage_asset('NewTheme/images/favicon.ico') }}">
    <link href="{{ storage_asset('theme/css/style.css') }}" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&family=Roboto:wght@100;300;400;500;700;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css">
    <link rel="stylesheet" href="{{ storage_asset('/theme/vendor/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ storage_asset('themeAdmin/css/typography.css') }}">
    <link href="{{ storage_asset('themeAdmin/css/custom.css') }}" rel="stylesheet">
    <style type="text/css">
        .grecaptcha-badge {
            z-index: 1000;
        }

        .authincation-content {
            background-color: #2B2B2B;
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

        .form-group label {
            color: #000;
        }

        .form-control {
            border-radius: 0px;
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

        .black-btn {
            background-color: #000;
            color: #fff;
        }

        .cancel-btn {
            border-color: #5a5a5a;
        }

        #creditCardType i {
            font-size: 26px;
            margin: 5px 0px 0px 5px;
        }
    </style>
</head>

<body class="h-100">
    <div class="authincation h-100">
        <div class="container h-100">
            <div class="row justify-content-center h-100vh align-items-center">
                <div class="col-md-6">
                    <div class="text-center mb-3">
                        <a href="javascript:;">
                            <img src="{{ storage_asset('NewTheme/images/Logo.png') }}" alt="" width="300px">
                        </a>
                    </div>
                    <div id="validation-errors"></div>
                    <div class="authincation-content">
                        <div class="row no-gutters">
                            <div class="col-xl-12">
                                <div class="auth-form row">
                                    <div class="col-xl-12">
                                        <h3 class="text-center mb-5">Pay with your UPI</h3>
                                    </div>
                                    <div class="col-xl-12 text-center">
                                        <h3 class="text-info">
                                            <strong>{{ $input['amount'] }}</strong> <small>{{ $input['currency']
                                                }}</small>
                                        </h3>
                                        <h6 class="text-info">{{ $input['email'] }}</h6>
                                    </div>
                                    <div class="col-xl-12 mt-3">
                                        <form action="{{ route('api.v2.testUPISubmit', $session_id) }}"
                                            id="extra-details-form" method="post">
                                            @csrf
                                            <div class="auth-form">
                                                <div class="row">
                                                    <div class="col-sm-12 col-md-12">
                                                        <div class="row">
                                                            <h3>UPI details</h3>
                                                            @if (in_array('upi', $data))
                                                            <div class="col-md-12 form-group">
                                                                <label class="mb-1">upi</label>
                                                                <input class="form-control required-field" name="upi"
                                                                    type="text" id="upi"
                                                                    placeholder="Ex. 9898098980@upi" minlength="2"
                                                                    maxlength="50" value="{{ $input['upi'] ?? null }}"
                                                                    required data-missing="UPI field is required">
                                                                @if ($errors->has('upi'))
                                                                <span class="help-block">
                                                                    <strong>{{ $errors->first('upi') }}</strong>
                                                                </span>
                                                                @endif
                                                            </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <strong class="text-danger">Note: <small>This is test API request. In a
                                                        live request, you will be asked to approve the transaction in
                                                        your UPI application.</small></strong>
                                            </div>
                                            <div class="pd-25">
                                                <div class="common-btns fl-btns">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <button type="button" id="submit-button"
                                                                class="btn black-btn btn-block">Pay Now</button>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <button type="button" id="cancel-button"
                                                                class="btn cancel-btn btn-block">Cancel</button>
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
                url: '{{ route('api.v2.testUPISubmit', $session_id) }}',
                type: 'post',
                data: data,
                beforeSend: function() {
                    $('#validation-errors').html('');
                },
                success: function (data) {
                    if (data.status == 'success') {
                        window.location = data.url;
                    } else {
                        $('#validation-errors').append('<div class="alert alert-danger">'+data.message+'</div');
                    }
                },
                fail: function(err) {
                    $('#validation-errors').append('<div class="alert alert-danger">'+data.message+'</div');
                },
                error: function(jqXHR, exception) {
                    $('#validation-errors').append('<div class="alert alert-danger">something went wrong, please try again</div');
                }
            });
        });
        $(document).on('click', '#cancel-button', function(e) {
            window.location = "{{ route('api.v2.test-decline', $session_id) }}";
        });
    </script>
    <script type="text/javascript">
        function submitDisabled() {
            var empty = false;
            $('.required-field').each(function() {
                if ($(this).val().length < 2) {
                    empty = true;
                }
            });
            if (empty == true) {
                $('#submit-button').prop('disabled', true);
            } else {
                $('#submit-button').prop('disabled', false);
            }
        }
        $(document).on('input', '.required-field', function(e) {
            submitDisabled();
        });
        $(document).on('change', '.required-field', function(e) {
            submitDisabled();
        });
        submitDisabled();
    </script>
</body>

</html>