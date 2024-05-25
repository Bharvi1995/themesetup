<!DOCTYPE html>
<html lang="en">
  <head>

    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Meta -->
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ storage_asset('NewTheme/assets/img/logo01.png') }}">

    <title>{{ config('app.name') }} Checkout Form</title>

    <!-- vendor css -->
    <link href="{{ storage_asset('NewTheme/assets/lib/@fortawesome/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ storage_asset('NewTheme/assets/lib/ionicons/css/ionicons.min.css') }}" rel="stylesheet">
<link href="{{ storage_asset('NewTheme/assets/lib/select2/css/select2.min.css') }}" rel="stylesheet">
    <!-- DashForge CSS -->
      <link rel="stylesheet" href="{{ storage_asset('NewTheme/assets/css/dashforge.css') }}">
    <link rel="stylesheet" href="{{ storage_asset('NewTheme/assets/css/dashforge.demo.css') }}">

    <style type="text/css">
      footer ul{
        margin: 0px;
      }
      footer li{
        float: left;
        list-style: none;
        height: 60px;
        position: relative;
        width: 80px;
      }
      footer li img{
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
      }
      .content-fixed{
        margin-top: 0px;
      }
    </style>
  </head>
  <body>
    <div class="container">
      <div class="row">
        <div class="col-md-12 text-center">
          <img src="{{ storage_asset('NewTheme/assets/img/logo02.png') }}" class="img-fluid" alt="" width="250px" style="margin: auto; margin-top: 5rem; margin-bottom: 5rem;">
        </div>
        <div class="col-md-12 text-center">
            {!! Form::open(array('route' => 'testwyre')) !!}
            <input type="hidden" name="reservation" id="reservation" value="{{$reservation}}"/>
            <input type="hidden" name="OrderID" id="OrderID" value="{{ $OrderID }}"/>

            <div class="col-md-12">
              <div class="form-group row">
                <label for="smsNeeded" class="col-sm-2 col-form-label">Enter SMS Code</label>
                <div class="col-md-4">
                  <input type="text" class="form-control" maxlength="6" name="smsNeeded" id="smsNeeded" placeholder="smsNeeded">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-success btn-sm" id="disableBTN">Submit</button>
                </div>
              </div>
            </div>
            {!! Form::close() !!}
        </diiv>
      </div>
    </div>
    <script type="text/javascript" src="{{ storage_asset('themeAdmin/js/jquery-latest.min.js') }}"></script>
    <script src="{{ storage_asset('NewTheme/assets/lib/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ storage_asset('NewTheme/assets/lib/feather-icons/feather.min.js') }}"></script>
    <script src="{{ storage_asset('NewTheme/assets/lib/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ storage_asset('NewTheme/assets/lib/select2/js/select2.min.js') }}"></script>
    <script src="{{ storage_asset('NewTheme/assets/js/dashforge.js') }}"></script>
  </body>
</html>
