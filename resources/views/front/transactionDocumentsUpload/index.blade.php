<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ config('app.name') }} | Upload Document</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ storage_asset('NewTheme/images/favicon.ico') }}">

    <link rel="stylesheet" href="{{ storage_asset('themeAdmin/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css"
        integrity="sha512-3pIirOrwegjM6erE5gPSwkUzO+3cTjpnV9lexlNZqvupR64iZBnOOTiiLPb9M36zpMScbmUNIcHUqKD47M719g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Typography CSS -->
    <link rel="stylesheet" href="{{ storage_asset('themeAdmin/css/typography.css') }}">
    <!-- Style CSS -->
    <link rel="stylesheet" href="{{ storage_asset('themeAdmin/css/style.css') }}">
    <!-- Responsive CSS -->
    <link rel="stylesheet" href="{{ storage_asset('themeAdmin/css/responsive.css') }}">

    <link rel="stylesheet" href="{{ storage_asset('themeAdmin/css/flatpickr.min.css') }}">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css"
        integrity="sha512-mSYUmp1HYZDFaVKK//63EcZq4iFWFjxSL+Z3T/aCt4IO9Cejm03q3NKKYN6pFQzY0SBOr8h+eCIAZHPXcpZaNw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" type="text/css" href="{{ storage_asset('NewTheme/css/custom.css') }}">

    <style type="text/css">
        .grecaptcha-badge {
            z-index: 1000;
        }

        .auth-form .text-danger {
            color: #842e2e !important;
        }
    </style>

    <script type="text/javascript">
        var current_page_url = "<?php echo URL::current(); ?>";
        var current_page_fullurl = "<?php echo URL::full(); ?>";
        var CSRF_TOKEN='{{ csrf_token() }}';
    </script>
    <script>
        var clicky_site_ids = clicky_site_ids || []; clicky_site_ids.push(101164380);
    </script>
    <script async src="//static.getclicky.com/js"></script>
</head>

<body class="h-100">
    <div class="authincation h-100">
        <div class="container h-100">
            <div class="row justify-content-center h-100 align-items-center">
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
                                    @if(\Session::get('success'))
                                    <div class="alert alert-success alert-dismissible" role="alert">
                                        <button type="button" class="close" data-dismiss="alert">×</button>
                                        <div class="alert-message">
                                            <span>{{ \Session::get('success') }}</span>
                                        </div>
                                    </div>
                                    @endif
                                    {{ \Session::forget('success') }}
                                    @if($errors->any())
                                    <div class="alert alert-danger alert-dismissible" role="alert">
                                        <button type="button" class="close" data-dismiss="alert">×</button>
                                        <div class="alert-message">
                                            @foreach ($errors->all() as $error)
                                            <p style="margin: 0px;">{{ $error }}</p>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                    @if($message = Session::get('error'))
                                    <div class="alert alert-warning alert-dismissible" role="alert">
                                        <button type="button" class="close" data-dismiss="alert">×</button>
                                        <div class="alert-message">
                                            <span><strong>Error!</strong> {{ $message }}</span>
                                        </div>
                                    </div>
                                    @endif
                                    {!! Session::forget('error') !!}
                                    <div class="card">
                                        <div class="card-body br-25">
                                            <strong class="text-primary">Order Id :</strong> {{ $data->order_id }}<br>
                                            <strong class="text-primary">Email :</strong> {{ $data->email }}<br>
                                            <strong class="text-primary">Amount :</strong> {{ $data->amount }} - {{ $data->currency }}
                                        </div>
                                    </div>
                                    <div class="card mt-3">
                                        <div class="card-body br-25">
                                            <form class="" action="{{ route('transaction-documents-upload') }}"
                                                method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <input type="hidden" name="transaction_id"
                                                    value="{{$request->transactionId}}">
                                                <input type="hidden" name="files_for" value="{{$request->uploadFor}}">
                                                <div
                                                    class="row form-group {{ $errors->has('files') ? ' has-error' : '' }}">
                                                    <div class="col-md-12">
                                                        <label class="control-label" for="files">Upload Document</label>
                                                        <div class="input-group">
                                                            <div class="custom-file">
                                                                <input type="file" class="custom-file-input filestyle"
                                                                    name="files[]" data-buttonname="btn-inverse"
                                                                    accept="image/png, image/jpeg, .pdf, .txt, .doc, .docx, .xls, .xlsx"
                                                                    id="inputGroupFile1" multiple="multiple">
                                                                <label class="custom-file-label"
                                                                    for="inputGroupFile1">Choose file</label>
                                                            </div>
                                                        </div>
                                                        @if ($errors->has('files'))
                                                        <p class="text-danger">
                                                            <strong>{{ $errors->first('files') }}</strong>
                                                        </p>
                                                        @endif
                                                        <br>
                                                        <span class="text-danger">Note : Please login to your {{
                                                            config('app.name') }} dashboard to upload multipal
                                                            files.</span><br>
                                                        <span class="text-primary">Login URL :</span> <a href="{{ Config('app.url')}}"
                                                            target="_blank">{{
                                                            Config('app.url')}}</a><br>
                                                        <br>
                                                        <button type="submit" class="btn btn-success">Upload</button>
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


    <!--**********************************
        Scripts
    ***********************************-->
    <!-- Required vendors -->
    <script src="{{ storage_asset('theme/vendor/global/global.min.js') }}"></script>
    <script src="{{ storage_asset('theme/js/custom.min.js') }}"></script>
    <script src="{{ storage_asset('theme/js/deznav-init.js') }}"></script>

    <script src="{{ storage_asset('themeAdmin/js/jquery-latest.min.js') }}"></script>
    <script src="{{ storage_asset('themeAdmin/assets/custom_js/front/transactionDocumentsUpload.js') }}"></script>
</body>

</html>