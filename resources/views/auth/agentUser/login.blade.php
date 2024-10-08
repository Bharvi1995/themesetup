<!DOCTYPE html>
<html class="loading dark-layout" lang="en" data-textdirection="ltr">
<head>
   <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    @if(config('app.env') == 'production')
        <link rel="shortcut icon" type="image/x-icon" href="{{ storage_asset('softtheme/img/favicon.ico') }}">
    @endif
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <link href="{{ storage_asset('softtheme/css/nucleo-icons.css')}}" rel="stylesheet" />
    <link href="{{ storage_asset('softtheme/css/nucleo-svg.css')}}" rel="stylesheet" />
    <link href="{{ storage_asset('softtheme/css/nucleo-svg.css')}}" rel="stylesheet" />
    <link id="pagestyle" href="{{ storage_asset('softtheme/css/soft-ui-dashboard.min.css')}}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/fontawesome.min.css"/>
</head>
<body class="light-theme">
   <main class="main-content  mt-0">
      <section>
         <div class="page-header min-vh-85">
            <div class="container">
               <div class="row">
                  <div class="col-xl-4 col-lg-5 col-md-6 d-flex flex-column mx-auto">
                     <div class="card card-plain mt-8">
                        <div class="card-header pb-0 text-left bg-transparent">
                           <h3 class="font-weight-bolder text-info text-gradient">Welcome back</h3>
                           <p class="mb-0">Enter your email and password to sign in</p>
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
                        
                           <form action="{{ URL::route('rp/login') }}" id="login-form" method="post">
                            {!! csrf_field() !!}
                                <label>Email</label>
                                <div class="mb-3">
                                    <input type="email" class="form-control" placeholder="Email" name="email" aria-label="Email" aria-describedby="email-addon">
                                    @if ($errors->has('email'))
                                        <div class="error-input">
                                            {{ $errors->first('email') }}
                                        </div>
                                    @endif
                                </div>
                              <label>Password</label>
                              <div class="mb-3">
                                 <input type="password" class="form-control" name="password" id="password" placeholder="Password" aria-label="Password" aria-describedby="password-addon">
                                 @if ($errors->has('password'))
                                    <div class="error-input">
                                        {{ $errors->first('password') }}
                                    </div>
                                @endif
                              </div>
                              <div class="form-check text-center form-switch">
                                <p class="mb-4 text-sm mx-auto text-right" >
                                    <a href="{{route('rp-password-reset')}}" class="text-info text-gradient font-weight-bold">Forgot Password?</a>
                                </p>
                              </div>
                              <div class="text-center">
                                    @if(config('app.env') == 'production')
                                        <button type="submit" class="btn bg-gradient-info w-100 mt-4 mb-0 g-recaptcha" data-sitekey="{{ config('app.captch_sitekey') }}" data-callback="onSubmit" data-action="submit">Sign in</button>
                                    @else
                                        <button type="submit" class="btn bg-gradient-info w-100 mt-4 mb-0">Sign in</button>
                                    @endif
                              </div>
                           </form>
                        </div>
                        <div class="card-footer text-center pt-0 px-lg-2 px-1">
                           <p class="mb-4 text-sm mx-auto">
                              Don't have an account?
                              <a href="{{route('agent.register')}}" class="text-info text-gradient font-weight-bold" >Sign up</a>
                           </p>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="oblique position-absolute top-0 h-100 d-md-block d-none me-n8">
                        <div class="oblique-image bg-cover position-absolute fixed-top ms-auto h-100 z-index-0 ms-n6" style="background-image:url('../storage/softtheme/img/curved-images/curved6.jpg')"></div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </section>
   </main>
   <footer class="footer py-5">
      <div class="container">
         <div class="row">
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


    <script src="{{ storage_asset('themesetup/assets/vendor/js/jquery-3.6.0.min.js')}}"></script>
    <script src="{{ storage_asset('softtheme/js/core/popper.min.js')}}"></script>
    <script src="{{ storage_asset('softtheme/js/core/bootstrap.min.js')}}"></script>
    <script src="{{ storage_asset('softtheme/js/plugins/perfect-scrollbar.min.js')}}"></script>
    <script src="{{ storage_asset('softtheme/js/plugins/smooth-scrollbar.min.js')}}"></script>
    <script src="{{ storage_asset('softtheme/js/soft-ui-dashboard.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/js/fontawesome.min.js"></script>
    <script src="https://www.google.com/recaptcha/api.js"></script>
    <script>
        $('#pwd-show').on('click', function () {
            var pwd = $('#password').attr('type');
            if(pwd == 'password'){
                $('#password').attr('type', 'text');
            }else{
                $('#password').attr('type', 'password');
            }
        });
        $("#password").focus(function(){
            $(this).attr('type','password');
        });

        jQuery(document).ready(function () {
            jQuery("#load").fadeOut();
            jQuery("#loading").delay().fadeOut("");
        });
    </script>
    <script>
        function onSubmit(token) {
            document.getElementById("login-form").submit();
        }
    </script>
    <!-- for demo purpose -->
</body>


<!-- END: Body-->
</html>