
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    @if(config('app.env') == 'production')
        <link rel="shortcut icon" type="image/x-icon" href="{{ storage_asset('softtheme/img/favicon.ico') }}">
    @endif
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <link href="{{ storage_asset('softtheme/css/nucleo-icons.css')}}" rel="stylesheet" />
    <link href="{{ storage_asset('softtheme/css/nucleo-svg.css')}}" rel="stylesheet" />
    <link href="{{ storage_asset('softtheme/css/nucleo-svg.css')}}" rel="stylesheet" />
    <link id="pagestyle" href="{{ storage_asset('softtheme/css/soft-ui-dashboard.min.css')}}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/fontawesome.min.css"/>
    <style type="text/css">
        .navbar-brand-img{
            max-width: 200px !important;
        }
    </style>
</head>
<body class="light-theme">
    <nav class="navbar navbar-expand-lg position-absolute top-0 z-index-3 w-100 shadow-none my-3 navbar-transparent mt-4">
    <div class="container">
      <a class="navbar-brand font-weight-bolder ms-lg-0 ms-3 text-white" href="{{ route('register') }}">
        <img src="{{ storage_asset('softtheme/img/Logo.png')}}" class="navbar-brand-img h-100" alt="main_logo">
      </a>
      <button class="navbar-toggler shadow-none ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#navigation" aria-controls="navigation" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon mt-2">
      <span class="navbar-toggler-bar bar1"></span>
      <span class="navbar-toggler-bar bar2"></span>
      <span class="navbar-toggler-bar bar3"></span>
      </span>
      </button>
   </div>
</nav>
<main class="main-content  mt-0">
   <section class="min-vh-100">
      <div class="page-header align-items-start min-vh-50 pt-5 pb-11 m-3 border-radius-lg" style="background-image: url('../storage/softtheme/img/curved-images/curved14.jpg');">
         <span class="mask bg-gradient-dark opacity-6"></span>
         <div class="container">
            <div class="row justify-content-center">
               <div class="col-lg-5 text-center mx-auto">
                  <h1 class="text-white mb-2 mt-5">Welcome!</h1>
                  <p class="text-lead text-white">Use these awesome forms to login or create new account in Merchant Panel for free.</p>
               </div>
            </div>
         </div>
      </div>
      <div class="container">
         <div class="row mt-lg-n10 mt-md-n11 mt-n10">
            <div class="col-xl-5 col-lg-5 col-md-7 mx-auto">
               <div class="card z-index-0">
                    <div class="card-header text-center pt-4">
                        <h5>Registration</h5>
                    </div>
                    <div class="card-body">
                    @if(\Session::get('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <div class="alert-body">
                            {{ \Session::get('success') }}
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">X</button>
                    </div>
                    @endif
                    {{ \Session::forget('success') }}
                    @if(\Session::get('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <div class="alert-body">
                            {{ \Session::get('error') }}
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">X</button>
                    </div>
                    @endif
                    {{ \Session::forget('error') }}
                    
                    <form action="{{ route('agent.store') }}" id="signup-form" method="post">
                        {!! csrf_field() !!}
                        @if (app('request')->input('RP') && app('request')->input('RP') != '')
                            <input type="hidden" name="RP" value="{{ app('request')->input('RP') }}">
                        @endif
                        <div class="mb-3">
                           <input type="text" class="form-control" placeholder="Name" aria-label="Name" aria-describedby="email-addon" name="name" value="{{ old('name') }}">
                           @if ($errors->has('name'))
                            <span class="help-block font-red-mint text-danger">
                                {{ $errors->first('name') }}
                            </span>
                            @endif
                        </div>
                        <div class="mb-3">
                           <input type="email" class="form-control" placeholder="Email" aria-label="Email" aria-describedby="email-addon" name="email" value="{{ old('email') }}">
                           @if ($errors->has('email'))
                            <span class="help-block font-red-mint text-danger">
                                {{ $errors->first('email') }}
                            </span>
                            @endif
                        </div>
                        <div class="mb-3">
                           <input type="password" class="form-control" placeholder="Password" name="password" aria-label="Password" aria-describedby="password-addon">
                           @if ($errors->has('password'))
                            <span class="help-block font-red-mint text-danger">
                                {{ $errors->first('password') }}
                            </span>
                            @endif
                        </div>
                        <div class="mb-3">
                           <input type="password" class="form-control" placeholder="Enter Confirm Password" name="password_confirmation" aria-label="Enter Confirm Password" aria-describedby="password-addon">
                        </div>
                        <!-- <div class="form-check form-check-info text-left">
                           <input class="form-check-input" type="checkbox" value id="flexCheckDefault" checked>
                           <label class="form-check-label" for="flexCheckDefault">
                           I agree the <a href="javascript:;" class="text-dark font-weight-bolder">Terms and Conditions</a>
                           </label>
                        </div> -->
                        <div class="text-center">
                           @if(config('app.env') == 'production')
                                <button class="btn bg-gradient-dark w-100 my-4 mb-2 g-recaptcha" data-sitekey="{{ config('app.captch_sitekey') }}" data-callback="onSubmit" data-action="submit">Sign up</button>
                           @else
                                <button class="btn bg-gradient-dark w-100 my-4 mb-2">Sign up</button>
                           @endif
                        </div>
                        <p class="text-sm mt-3 mb-0">Already have an account? <a href="{{route('rp/login')}}" class="text-dark font-weight-bolder">Sign in</a></p>
                     </form>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </section>
   <footer class="footer py-5">
      <div class="container">
         <div class="row">
            <!-- <div class="col-lg-8 mb-4 mx-auto text-center">
               <a href="javascript:;" target="_blank" class="text-secondary me-xl-5 me-3 mb-sm-0 mb-2">
               Company
               </a>
               <a href="javascript:;" target="_blank" class="text-secondary me-xl-5 me-3 mb-sm-0 mb-2">
               About Us
               </a>
               <a href="javascript:;" target="_blank" class="text-secondary me-xl-5 me-3 mb-sm-0 mb-2">
               Team
               </a>
               <a href="javascript:;" target="_blank" class="text-secondary me-xl-5 me-3 mb-sm-0 mb-2">
               Products
               </a>
               <a href="javascript:;" target="_blank" class="text-secondary me-xl-5 me-3 mb-sm-0 mb-2">
               Blog
               </a>
               <a href="javascript:;" target="_blank" class="text-secondary me-xl-5 me-3 mb-sm-0 mb-2">
               Pricing
               </a>
            </div>
            <div class="col-lg-8 mx-auto text-center mb-4 mt-2">
               <a href="javascript:;" target="_blank" class="text-secondary me-xl-4 me-4">
               <span class="text-lg fab fa-dribbble"></span>
               </a>
               <a href="javascript:;" target="_blank" class="text-secondary me-xl-4 me-4">
               <span class="text-lg fab fa-twitter"></span>
               </a>
               <a href="javascript:;" target="_blank" class="text-secondary me-xl-4 me-4">
               <span class="text-lg fab fa-instagram"></span>
               </a>
               <a href="javascript:;" target="_blank" class="text-secondary me-xl-4 me-4">
               <span class="text-lg fab fa-pinterest"></span>
               </a>
               <a href="javascript:;" target="_blank" class="text-secondary me-xl-4 me-4">
               <span class="text-lg fab fa-github"></span>
               </a>
            </div> -->
         </div>
         <div class="row">
            <div class="col-8 mx-auto text-center mt-1">
               <p class="mb-0 text-secondary">
                  © <script>
                    document.write(new Date().getFullYear())
                    </script>,
                    {{ config('app.name') }} Solution Team LTD.
               </p>
            </div>
         </div>
      </div>
   </footer>
</main>
    
    <script src="{{ storage_asset('themesetup/assets/vendor/js/jquery-3.6.0.min.js')}}"></script>
    <script src="{{ storage_asset('softtheme/js/core/popper.min.js')}}"></script>
    <script src="{{ storage_asset('softtheme/js/core/bootstrap.min.js')}}"></script>
    <script src="{{ storage_asset('softtheme/js/plugins/perfect-scrollbar.min.js')}}"></script>
    <script src="{{ storage_asset('softtheme/js/plugins/smooth-scrollbar.min.js')}}"></script>
    <script src="{{ storage_asset('softtheme/js/soft-ui-dashboard.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/js/fontawesome.min.js"></script>
    <script src="https://www.google.com/recaptcha/api.js"></script>
    <script>
        function onSubmit(token) {
            document.getElementById("signup-form").submit();
        }
    </script>
    <!-- for demo purpose -->
</body>
</html>