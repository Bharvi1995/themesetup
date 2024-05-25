<!DOCTYPE html>
<html class="loading dark-layout" lang="en" data-textdirection="ltr">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <title>{{ config('app.name') }} | {{ __('messages.pageTitleTestCard') }}</title>
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
    </style>
</head>

<body>
    <div id="loading">
        <p class="mt-1">{{ __('messages.loading') }}...</p>
    </div>
    <div class="app-content content">
        <div class="container">
            <div class="row content-body">
                <div class="col-md-6 col-xl-6 col-xxl-6">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <img src="{{ storage_asset('NewTheme/images/Logo.png') }}" alt=""
                                width="250px">
                            <h4 class="text-primary mt-2 mb-1">{{ __('messages.headingTest') }}</h4>
                        </div>
                    </div>

                    {{-- ajax error messages --}}
                    <div class="row">
                        <div class="align-items-center">
                            <div id="validation-errors" class="col-12 mx-auto"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body gateway-card">
                                    <form method="post"
                                        action="{{ route('api.v2.test-extraDetailsFormSubmit', $order_id) }}"
                                        name="extra_details_form" id="extra-details-form" class="validity">
                                        @csrf
                                        <input type="hidden" name="card_type" id="card-type">
                                        {{-- here comes form fields --}}
                                        <div id="form-fields"></div>
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
    <script src="{{ storage_asset('NewTheme/js/jquery.validity.js') }}"></script>
    <script src="{{ storage_asset('NewTheme/js/creditly.js') }}"></script>
    <script>
        jQuery(document).ready(function() {
            jQuery("#load").fadeOut();
            jQuery("#loading").delay().fadeOut("");
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
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            // card type visa
            $(document).find('#card-type').val(2);
            var data = $('#extra-details-form').serialize();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: '{{ route('api.v2.test-cardSelect', $order_id) }}',
                type: 'post',
                data: data,
                async: true,
                context: $(this),
                beforeSend: function() {
                    $('#validation-errors').html('');
                    $('#form-fields').html('');
                    $('.authincation').attr('id', 'authincation');
                    showLoader();
                },
                success: function(data) {
                    if (data.status == 'success') {
                        $('#form-fields').html(data.html);
                    } else {
                        $('#validation-errors').append(
                            '<div class="alert alert-danger"><div class="alert-body">' + data
                            .message + '</div></div>');
                        $('#submit-button').prop('disabled', true);
                    }
                },
                fail: function(err) {
                    $('#validation-errors').append(
                        '<div class="alert alert-danger"><div class="alert-body">' + data.message +
                        '</div></div>');
                    $('#submit-button').prop('disabled', true);
                },
                error: function(jqXHR, exception) {
                    $('#validation-errors').append(
                        '<div class="alert alert-danger"><div class="alert-body">Something went wrong, please try again</div></div>'
                    );
                    $('#submit-button').prop('disabled', true);
                }
            }).always(function(jqXHR, exception) {
                $('.authincation').attr('id', 'authincationer');
                hideLoader();
            });
            // end card type visa

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
                    url: '{{ route('api.v2.test-extraDetailsFormSubmit', $order_id) }}',
                    type: 'post',
                    data: data,
                    beforeSend: function() {
                        $('#validation-errors').html('');
                        showLoader();
                    },
                    success: function(data) {
                        if (data.status == 'success') {
                            window.location = data.url;
                        } else if (data.errors) {
                            $.each(data.errors, function(key, value) {
                                $('#validation-errors').append(
                                    '<div class="alert alert-danger"><div class="alert-body">' +
                                    value + '</div></div>');
                                $('#submit-button').prop('disabled', true);
                            });
                        } else {
                            $('#validation-errors').append(
                                '<div class="alert alert-danger"><div class="alert-body">' +
                                data.message + '</div></div>');
                            $('#submit-button').prop('disabled', true);
                        }
                    },
                    fail: function(err) {
                        $('#validation-errors').append(
                            '<div class="alert alert-danger"><div class="alert-body">' +
                            data.message + '</div></div>');
                        $('#submit-button').prop('disabled', true);
                    },
                    error: function(jqXHR, exception) {
                        $('#validation-errors').append(
                            '<div class="alert alert-danger"><div class="alert-body">something went wrong, please try again</div></div>'
                        );
                        $('#submit-button').prop('disabled', true);
                    }
                }).always(function(jqXHR, exception) {
                    hideLoader();
                });
            });

            // submit form
            $(document).on('click', '#cancel-button', function(e) {
                window.location = "{{ route('api.v2.test-decline', $order_id) }}";
            });
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

        function showLoader() {
            jQuery("#load").fadeIn();
            jQuery("#loading").delay().fadeIn();
        }

        function hideLoader() {
            jQuery("#load").fadeOut();
            jQuery("#loading").delay().fadeOut();
        }
        $(document).on('input', '.required-field', function(e) {
            submitDisabled();
        });
        $(document).on('change', '.required-field', function(e) {
            submitDisabled();
        });
    </script>
</body>

</html>
