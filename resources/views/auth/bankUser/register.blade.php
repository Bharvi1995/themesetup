<!doctype html>
<html class="loading dark-layout" lang="en" data-textdirection="ltr">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <title>{{ config('app.name') }} | Bank Register</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ storage_asset('NewTheme/images/favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" integrity="sha512-5A8nwdMOWrSz20fDsjczgUidUBR8liPYU+WymTZP1lmY9G6Oc7HlZv156XqnsgNUzTyMefFTcsFH/tnJE/+xBg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- BEGIN: Theme CSS-->

    <link rel="stylesheet" type="text/css" href="{{ storage_asset('NewTheme/css/bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('NewTheme/css/bootstrap-extended.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('NewTheme/css/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('NewTheme/css/auth.css') }}">
</head>

<body>
    <!-- loader Start -->
    <div id="loading">
        <p class="mt-1">Loading...</p>
    </div>
    <!-- loader END -->
    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="container">
            <div class="row m-0">
                <div class="col-md-4 col-xl-4 col-xxl-4 offset-md-4 offset-xl-4 offset-xxl-4 content-body">
                    <div class="row content-box-form">
                        <div class="col-md-12 text-center mb-2">
                            <img src="{{ storage_asset('NewTheme/images/Logo.png') }}" width="260px">
                        </div>
                        <div class="col-md-12 form-contant-right">
                            <h3>Sign Up</h3>
                            <p>Please provide all required details to register your business with us.</p>
                            <form action="{{ route('bank.store') }}" id="signup-form" method="post">
                                {!! csrf_field() !!}

                                @if(\Session::get('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <div class="alert-body">
                                        {{ \Session::get('success') }}
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                                @endif
                                {{ \Session::forget('success') }}
                                @if(\Session::get('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <div class="alert-body">
                                        {{ \Session::get('error') }}
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                                @endif

                                <div class="form-group mt-1">
                                    <label>Name</label>
                                    <input type="text" class="form-control" name="bank_name" placeholder="Enter here"
                                        autocomplete="off" value="{{ old('bank_name') }}">
                                    @if ($errors->has('bank_name'))
                                    <span class="help-block font-red-mint text-danger">
                                        {{ $errors->first('bank_name') }}
                                    </span>
                                    @endif
                                </div>

                                <div class="form-group mt-1">
                                    <label>Email</label>
                                    <input type="email" class="form-control" name="email" placeholder="Enter here"
                                        autocomplete="off" value="{{ old('email') }}">
                                    @if ($errors->has('email'))
                                    <span class="help-block font-red-mint text-danger">
                                        {{ $errors->first('email') }}
                                    </span>
                                    @endif
                                </div>                                

                                <div class="form-group mt-1">
                                    <label>Password</label>
                                    <input type="password" class="form-control" placeholder="Enter here" name="password"
                                        autocomplete="off">
                                    <small>The password must contain: One Upper, Lower, Numeric and Special Character.</small>
                                    @if ($errors->has('password'))
                                    <br>
                                    <span class="help-block font-red-mint text-danger">
                                        {{ $errors->first('password') }}
                                    </span>
                                    @endif
                                </div>

                                <div class="form-group mt-1">
                                    <label>Confirm Password</label>
                                    <input type="password" class="form-control" placeholder="Enter here"
                                        name="password_confirmation" autocomplete="off">
                                    @if ($errors->has('password_confirmation'))
                                    <span class="help-block font-red-mint text-danger">
                                        {{ $errors->first('password_confirmation') }}
                                    </span>
                                    @endif
                                </div>                                
                                <div class="form-group mt-1">
                                    <label>Country</label>
                                    {!! Form::select('country', [''=>'Select']+getCountry(), null,
                                    array('class' => 'form-control
                                    select2','id'=>'country','data-width'=>'100%')) !!}
                                    @if ($errors->has('country'))
                                    <span class="text-danger help-block form-error">
                                        {{ $errors->first('country') }}
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group mt-1">
                                    <label>Processing Country</label>
                                    {!! Form::select('processing_country[]',
                                    ['UK'=>'UK','EU'=>'EU','US/CANADA'=>'US/CANADA','Others'=>'Others'],isset($data->processing_country)
                                    ? json_decode($data->processing_country) : [], array('class' =>
                                    'form-control select2','multiple' => 'multiple')) !!}
                                    @if ($errors->has('processing_country'))
                                    <span class="text-danger help-block form-error">
                                        {{ $errors->first('processing_country') }}
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group mt-1">
                                    <label>Industry Type</label>
                                    {!! Form::select('category_id[]', $category, isset($data->category_id) ?
                                    json_decode($data->category_id) : [], array('id' => 'category_id','class'
                                    =>'form-control select2','multiple' => 'multiple')) !!}
                                    @if ($errors->has('category_id'))
                                    <span class="text-danger help-block form-error">
                                        {{ $errors->first('category_id') }}
                                    </span>
                                    @endif
                                </div>

                                <div class="d-inline-block w-100">
                                    <button class="btn btn-danger mt-2 w-100 g-recaptcha"
                                        data-sitekey="{{ config('app.captch_sitekey') }}" data-callback='onSubmit'
                                        data-action='submit'>Register</button>
                                </div>
                            </form>
                        </div>

                        <div class="col-md-12 mt-2">
                            <p class="text-center">Have already an account? <a href="{{route('bank/login')}}" class="text-primary"> Sign In </a> </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END: Content-->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.js'></script>    
    <script src="https://www.google.com/recaptcha/api.js"></script>

    <script src="{{ storage_asset('NewTheme/vendors/js/forms/select/select2.full.min.js') }}"></script>
    <script src="{{ storage_asset('NewTheme/js/form-select2.js') }}"></script>

    <script>
        jQuery(document).ready(function () {
            jQuery("#load").fadeOut();
            jQuery("#loading").delay().fadeOut("");
        });
    </script>
    <script>
        function onSubmit(token) {
            document.getElementById("signup-form").submit();
        }        
    </script>

    <script type="text/javascript">
        $(document).ready(function() {
            $('.select2').select2();
        });

        $("#category_id").select2({
            placeholder: "Select"
        });
    </script>
</body>
</html>