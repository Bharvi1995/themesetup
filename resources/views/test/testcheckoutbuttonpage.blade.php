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

                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <a href="{{ route('api-testing') }}" class="btn btn-info" style="margin-top: 100px;">Pay Now</a>
                                </div>
                            </div>
                        </div>
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
