<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1.0, shrink-to-fit=no">
    <title>{{ config('app.name') }} | Merchant Wordpress Plugin Installation Document</title>
    <link rel="icon" type="image/png" sizes="16x16" href="{{ storage_asset('NewTheme/images/favicon.ico') }}">
    <link rel="shortcut icon" href="{{ storage_asset('NewTheme/images/favicon.ico') }}" />

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&family=Roboto:wght@100;300;400;500;700;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css"
        integrity="sha512-5A8nwdMOWrSz20fDsjczgUidUBR8liPYU+WymTZP1lmY9G6Oc7HlZv156XqnsgNUzTyMefFTcsFH/tnJE/+xBg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Plugins CSS -->
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.2/styles/atom-one-dark.min.css">
    <link id="theme-style" rel="stylesheet"
        href="{{ storage_asset('NewTheme/API-Doc/plugins/simplelightbox/simple-lightbox.min.css') }}">

    <!-- Theme CSS -->
    <link id="theme-style" rel="stylesheet" href="{{ storage_asset('NewTheme/API-Doc/css/theme.css') }}">
    <link id="theme-style" rel="stylesheet" href="{{ storage_asset('NewTheme/API-Doc/css/custom.css') }}">
    <style type="text/css">
        h1, .h1, h2, .h2, h3, .h3, h4, .h4, h5, .h5, h6, .h6{
            color: #262626 !important;
        }
    </style>
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('theme/API-assets/css/stylesheet.css') }}" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/styles/atom-one-dark.min.css">
</head>

<body class="docs-page">
    <header class="header fixed-top">
        <div class="branding docs-branding">
            <div class="container-fluid position-relative py-2">
                <div class="docs-logo-wrapper">
                    <div class="site-logo">
                        <a class="navbar-brand" href="{{ url('dashboard') }}">
                            <img class="logo-icon me-2" src="{{ storage_asset('NewTheme/images/Logo.png') }}" alt="logo" width="150px">
                        </a>
                    </div>
                </div>
                <!--//docs-logo-wrapper-->
                <div class="docs-top-utilities d-flex justify-content-end align-items-center">
                    @if (Auth::check())
                        <a target="_blank" href="{{ url('dashboard') }}"
                            class="btn btn-primary d-none d-lg-flex">Dashboard</a>
                    @else
                        <a target="_blank" href="{{ url('login') }}" class="btn btn-primary d-none d-lg-flex">Sign
                            In</a>
                    @endif
                    &nbsp; &nbsp;
                    <a href="{{ route('api-document') }}" class="btn btn-primary d-none d-lg-flex">Back</a>
                </div>
            </div>
        </div>
    </header>


    <div class="docs-wrapper">
        <div id="docs-sidebar" class="docs-sidebar">
            <nav id="docs-nav" class="docs-nav navbar">
                <ul class="section-items list-unstyled nav flex-column pb-3">
                    <li class="nav-item section-title">
                        <a class="nav-link scrollto active" href="#APIRequestIn">
                            <span class="theme-icon-holder me-2">
                                <i class="fa fa-file-code-o"></i>
                            </span>
                            Wordpress Plugin Installation
                        </a>
                    </li>
                    <li class="nav-item"><a class="nav-link scrollto" href="#APIRequest">Documentation</a></li>
                    <li class="nav-item"><a class="nav-link scrollto" href="#APIResponse">Download</a></li>
                </ul>

            </nav>
            <!--//docs-nav-->
        </div>
        <!--//docs-sidebar-->
        <div class="docs-content">
            <div class="container">
                <article class="docs-article" id="APIRequestIn">
                    <section class="docs-section" id="APIRequest">
                        <h2>Installation Documentation</h2>
                        <div class="card-body">
                            <p><strong>Note:</strong> Woocommerce installation is required to use Payment plugin, please install Woocommerce first. Then follow the below steps to install Wordpress plugin.</p>
                            <div class="g-3">
                                <ol>
                                    <li>Install plugin from below link</li>
                                    <li>
                                        Open your WordPress Admin Panel. Just go to the <kbd>Plugins</kbd> and <kbd>Add New Plugin</kbd> then click on the Upload Plugin button. You will find it just next to the Add Plugins title.
                                    </li>
                                    <li>
                                        Now click on the Choose file button then choose the plugin zip file then click on the Install Now button.
                                    </li>
                                    <li>
                                        After the plugin installed, go to the WordPress admin panel and then navigate the <kbd>WooCommerce > Settings > Payments</kbd> menu.
                                    </li>
                                    <li>
                                        Once you open the WooCommerce Payments setting page, you will see the testpay Payment method. Just enable the testpay Payment method by click on the switch toggle button and enable it.
                                    </li>
                                    <li>
                                        Now, Click on the Manage or Set up button to set up the testpay payment method. You must require the valid API Key to enable the testpay payment method. You can easily find the API Key from your testpay account. Once you go to the testpay payment method setting page you will see the interface like below.
                                    </li>
                                </ol>
                            </div>
                            <div class="row g-3">
                                <h2>Options</h2>
                                <ul>
                                    <li><strong>Enable/Disable </strong>: You can easily Enable/Disable the payment method using the Enable testpay Payment method checkbox</li>
                                    <li><strong>Method Title</strong>: You can set the custom payment method title for your customers. It will show on the checkout page.</li>
                                    <li><strong>Customer Message</strong>: You can add a custom message for your customers.</li>
                                    <li><strong>Test Mode</strong>: Enable Test Mode when you would like to test the testpay payment method and performance a test transaction using test card detail.</li>
                                    <li><strong>API Key</strong>: You must enter the API key to make working the testpay Payment method. You can't even perform a test transactions without API Key.</li>
                                    <li><strong>Order Status After The Checkout</strong>: You can customize the order status set to change the order automatically after payment has been received successfully.</li>
                                </ul>
                            </div>
                        </div>
                    </section>
                    <!--//section-->

                    <section class="docs-section" id="APIResponse">
                        <h2>Download Plugin</h2>
                        <div class="row g-3 text-center">
                            <form action="{{ route('wpPlugin.download') }}" method="post" class="basic-form">
                                @csrf
                                <div class="col-md-12">
                                    <a target="_blank" href="{{ url('storage/plugin/woocommerce-testpay-payment-gateway.zip') }}" class="btn btn-lg btn-primary mt-1 mb-3">Download Plugin</a>
                                    {{-- <button type="submit" class="btn btn-lg btn-primary mt-1 mb-3">Download Plugin</button> --}}
                                </div>
                            </form>
                        </div>
                    </section>
                    <!--//section-->
                </article>

                <footer class="footer">
                    <div class="footer-bottom text-center py-3">
                        <small class="copyright">Copyright &copy; Designed &amp; Developed by <a class="theme-link"
                                href="{{ config('app.url') }}" target="_blank">{{ config('app.name') }}</a>
                            {{ date('Y') }}</small>
                    </div>
                </footer>
            </div>
        </div>
    </div>
    <!--//docs-wrapper-->


    <!-- Javascript -->
    <script src="{{ storage_asset('NewTheme/API-Doc/plugins/popper.min.js') }}"></script>
    <script src="{{ storage_asset('NewTheme/API-Doc/plugins/bootstrap/js/bootstrap.min.js') }}"></script>


    <!-- Page Specific JS -->
    <script src="{{ storage_asset('NewTheme/API-Doc/plugins/smoothscroll.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.8/highlight.min.js"></script>
    <script src="{{ storage_asset('NewTheme/API-Doc/js/highlight-custom.js') }}"></script>
    <script src="{{ storage_asset('NewTheme/API-Doc/plugins/simplelightbox/simple-lightbox.min.js') }}"></script>
    <script src="{{ storage_asset('NewTheme/API-Doc/plugins/gumshoe/gumshoe.polyfills.min.js') }}"></script>
    <script src="{{ storage_asset('NewTheme/API-Doc/js/docs.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/highlight.min.js"></script>
    <script>
        hljs.highlightAll();
    </script>

</body>

</html>
