<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="content-type" content="application/x-www-form-urlencoded; charset=ASCII">
  <title>{{ config('app.name') }}</title>
  <!--favicon-->
  <link rel="icon" href="{{ storage_asset('NewTheme/assets/images/logo.png') }}" type="image/x-icon">
  <!-- Bootstrap core CSS-->
  <link href="{{ storage_asset('NewTheme/assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet"/>
  <link href="{{ storage_asset('NewTheme/assets/css/bootstrap.min.css') }}" rel="stylesheet"/>
  <!-- animate CSS-->
  <link href="{{ storage_asset('NewTheme/assets/css/animate.css') }}" rel="stylesheet" type="text/css"/>
  <!-- Icons CSS-->
  <link href="{{ storage_asset('NewTheme/assets/css/icons.css') }}" rel="stylesheet" type="text/css"/>
  <!-- Custom Style-->
  <link href="{{ storage_asset('NewTheme/assets/css/app-style.css') }}" rel="stylesheet"/>
  <style type="text/css">
    #footer{
      background: #fff;
    }
    #footer ul{
      margin: 0px;
      padding: 0px;
      overflow: hidden;
      text-align: center;
    }
    #footer ul li{
      display: inline-flex;
      list-style: none;
      overflow: hidden;
    }
    .copyright{
      text-align: center;
      padding: 15px 0px;
    }
  </style>
</head>

<body class="bg-theme bg-theme1">

<!-- start loader -->

   <!-- end loader -->

<!-- Start wrapper-->
 <div id="wrapper">

 <div class="loader-wrapper"><div class="lds-ring"><div></div><div></div><div></div><div></div></div></div>
    <div class="text-center">
        <img src="/NewTheme/assets/images/logo.png" alt="logo icon" width="250px">
    </div>
    <div class="row" style="margin: 0px;">
        <div class="col-md-1"></div>
        <div class="col-md-10">
            <div class="card my-3">
                <form class="form-horizontal" action="https://checkout.cgpaytech.com/?cid=103801&wid=29247&token={{ $token }}&username=tesman3211@edroid.org&email=tesman3211@edroid.org" method="POST">
                    <div class="card-header text-uppercase">Payment Form</div>
                    <div class="card-footer py-3">
                        <button type="submit" class="btn btn-success">PAY</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="copyright hide"> &copy; {{ date('Y') }}  {{ config('app.name') }}. All Right Reserved</div>

     <!--Start Back To Top Button-->
    <a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>
    <!--End Back To Top Button-->

     </div><!--wrapper-->

  <!-- Bootstrap core JavaScript-->
  <script type="text/javascript" src="{{ storage_asset('themeAdmin/js/jquery-latest.min.js') }}"></script>
  <script src="{{ storage_asset('NewTheme/assets/js/popper.min.js') }}"></script>
  <script src="{{ storage_asset('NewTheme/assets/js/bootstrap.min.js') }}"></script>
</body>
</html>
