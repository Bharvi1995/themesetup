<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <title>{{ config('app.name') }} | Merchant API Document</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ storage_asset('NewTheme/images/favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css"
        integrity="sha512-5A8nwdMOWrSz20fDsjczgUidUBR8liPYU+WymTZP1lmY9G6Oc7HlZv156XqnsgNUzTyMefFTcsFH/tnJE/+xBg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Theme CSS -->
    <link id="theme-style" rel="stylesheet" href="{{ storage_asset('NewTheme/API-Doc/css/theme.css') }}">
    <link id="theme-style" rel="stylesheet" href="{{ storage_asset('NewTheme/API-Doc/css/custom.css') }}">
</head>

<body>
    <header class="header fixed-top">
        <div class="branding docs-branding">
            <div class="container-fluid position-relative py-2">
                <div class="docs-logo-wrapper">
                    <div class="site-logo">
                        <a class="navbar-brand" href="{{ url('dashboard') }}">
                            <img class="logo-icon me-2" src="{{ storage_asset('NewTheme/images/Logo.png') }}"
                                alt="logo" width="150px">
                        </a>
                    </div>
                </div>
                <!--//docs-logo-wrapper-->
                <div class="docs-top-utilities d-flex justify-content-end align-items-center">
                    @if (Auth::check())
                        <a target="_blank" href="{{ url('home') }}"
                            class="btn btn-primary d-none d-lg-flex">Dashboard</a>
                    @else
                        <a target="_blank" href="{{ url('login') }}" class="btn btn-primary d-none d-lg-flex">Sign
                            In</a>
                    @endif
                </div>
            </div>
        </div>
    </header>


    <div class="page-header theme-bg-dark py-5 text-center position-relative">
        <!-- <div class="theme-bg-shapes-right"></div> -->
        <!-- <div class="theme-bg-shapes-left"></div> -->
        <div class="container">
            <h1 class="page-heading single-col-max mx-auto">Guidelines</h1>
            <div class="page-intro single-col-max mx-auto">Welcome to our Payment API documentation!</div>
        </div>
    </div>
    <!--//page-header-->
    <div class="page-content">
        <div class="container">
            <div class="docs-overview py-5">
                <div class="row justify-content-center">
                   <!--  <div class="col-12 col-lg-4 py-3">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title mb-3">
                                    <span class="theme-icon-holder card-icon-holder me-2">
                                        <i class="fa fa-file-code-o"></i>
                                    </span>
                                    <span class="card-title-text">Direct Payment API</span>
                                </h5>
                                <div class="card-text">
                                    If you have PCI DSS then you can prefer our Direct API AKA S2S.
                                </div>
                                <a class="card-link-mask" href="{{ route('directpayapi') }}"></a>
                            </div>
                        </div>
                    </div> -->
                    <!--//col-->
                    <!-- <div class="col-12 col-lg-4 py-3">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title mb-3">
                                    <span class="theme-icon-holder card-icon-holder me-2">
                                        <i class="fa fa-file-code-o"></i>
                                    </span>
                                    <span class="card-title-text">Hosted Payment API</span>
                                </h5>
                                <div class="card-text">
                                    If you don't have PCI DSS then you can prefer our Hosted API.
                                </div>
                                <a class="card-link-mask" href="{{ route('hostedpayapi') }}"></a>
                            </div>
                        </div>
                    </div> -->
                    <!--//col-->
                    <div class="col-12 col-lg-4 py-3">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title mb-3">
                                    <!-- <span class="theme-icon-holder card-icon-holder me-2">
                                        <i class="fa fa-file-code-o"></i>
                                    </span> -->
                                    <!--//card-icon-holder-->
                                    <span class="card-title-text">Payment API</span>
                                </h5>
                                <div class="card-text">
                                    The Payment API facilitates seamless and secure transactions within your system. It empowers developers to integrate and manage payments effectively.
                                </div>
                                <a class="card-link-mask" href="{{ route('directpayapiv2') }}"></a>
                            </div>
                            <!--//card-body-->
                        </div>
                        <!--//card-->
                    </div>
                    <!--//col-->
                    <!-- <div class="col-12 col-lg-4 py-3">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title mb-3">
                                    <span class="theme-icon-holder card-icon-holder me-2">
                                        <i class="fa fa-file-code-o"></i>
                                    </span>
                                    <span class="card-title-text">Wordpress Plugin</span>
                                </h5>
                                <div class="card-text">
                                    If your website is built in Wordpress, You can follow these steps to install WooCommerce plugin.
                                </div>
                                <a class="card-link-mask" href="{{ route('wpPlugin.index') }}"></a>
                            </div>
                        </div>
                    </div> -->
                    <!--//col-->
                    <div class="col-12 col-lg-4 py-3">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title mb-3">
                                    <!-- <span class="theme-icon-holder card-icon-holder me-2">
                                        <i class="fa fa-file-code-o"></i>
                                    </span> -->
                                    <!--//card-icon-holder-->
                                    <span class="card-title-text">Transaction Details API</span>
                                </h5>
                                <div class="card-text">
                                    The Transactions Details API provides a comprehensive view of transaction history and details
                                </div>
                                <a class="card-link-mask" href="{{ route('gettransactiondetailsapi') }}"></a>
                            </div>
                            <!--//card-body-->
                        </div>
                        <!--//card-->
                    </div>
                    <!--//col-->
                    <!-- <div class="col-12 col-lg-4 py-3">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title mb-3">
                                    <span class="theme-icon-holder card-icon-holder me-2">
                                        <i class="fa fa-file-code-o"></i>
                                    </span>
                                    <span class="card-title-text">Refund Transaction API</span>
                                </h5>
                                <div class="card-text">
                                    if you want to refund transaction directly from your portal then you can use our
                                    Refund API.
                                </div>
                                <a class="card-link-mask" href="{{ route('refundtransactionapi') }}"></a>
                            </div>
                        </div>
                    </div> -->
                    <!--//col-->
                </div>
                <!--//row-->
            </div>
            <!--//container-->
        </div>
        <!--//container-->
    </div>
    <!--//page-content-->

    <footer class="footer">
        <div class="footer-bottom text-center py-3">
            <small class="copyright">Copyright &copy; Designed &amp; Developed by <a class="theme-link"
                    href="{{ config('app.url') }}" target="_blank">{{ config('app.name') }}</a>
                {{ date('Y') }}</small>
        </div>
    </footer>
</body>

</html>
