<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1.0, shrink-to-fit=no">
    <title>{{ config('app.name') }} | Merchant Refund Transaction API Document</title>
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
                            <img class="logo-icon me-2" src="{{ storage_asset('NewTheme/images/Logo.png') }}"
                                alt="logo" width="150px">
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
                            Refund Transaction API
                        </a>
                    </li>
                    <li class="nav-item"><a class="nav-link scrollto" href="#APIRequest">Request</a></li>
                    <li class="nav-item"><a class="nav-link scrollto" href="#APIResponse">Response</a></li>
                </ul>

            </nav>
            <!--//docs-nav-->
        </div>
        <!--//docs-sidebar-->
        <div class="docs-content">
            <div class="container">
                <article class="docs-article" id="APIRequestIn">
                    <section class="docs-section" id="APIRequest">
                        <header class="docs-header">
                            <h1 class="docs-heading">Transaction Refund API</h1>
                            <p class="lead">In order to mark a transaction as refund, you can call the below API.
                            </p>
                            <p class="lead">This API call will be done when you want to Refund a particular
                                transaction. After the API call, that Successful Transaction will be marked as
                                Refund.</p>
                            <p class="lead"><b>Note:</b> You can not request for refund of the transactions which is
                                already marked as suspicious or chargeback.</p>
                        </header>

                        <h4 class="mt-3">Request Parameter *</h4>
                        <div class="table-responsive my-4">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Paramters</th>
                                        <th>Required</th>
                                        <th style="width: 110px;">Data Type</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>api_key</td>
                                        <td>Yes</td>
                                        <td>String</td>
                                        <td>API key from your account.</td>
                                    </tr>
                                    <tr>
                                        <td>order_id</td>
                                        <td>Optional</td>
                                        <td>String</td>
                                        <td>Value you returned from Transaction API.</td>
                                    </tr>
                                    <tr>
                                        <td>customer_order_id</td>
                                        <td>Optional</td>
                                        <td>String</td>
                                        <td>Merchant side unique transaction Ref.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3 mt-3">
                                <strong>URL - </strong> <br><span
                                    class="text-danger-custom">{{ env('APP_URL') }}/api/refund</span>
                            </div>
                            <div class="col-md-12 mb-3">
                                <strong>Method - </strong> <code>POST</code>
                            </div>
                            <div class="col-md-12">
                                <p><strong>Note:</strong> <code>order_id</code> or
                                    <code>customer_order_id</code>, You should pass at-least one Parameter. Both
                                    Parameters can be passed as well.
                                </p>
                            </div>
                        </div>

                    </section>
                    <!--//section-->

                    <section class="docs-section" id="APIResponse">
                        <h2 class="section-heading">Response</h2>
                        <h6>DESCRIPTION OF SUCCESS JSON PARAMETER</h6>
                        <p class="mb-3"><code>status</code> : success means transaction found in our system. <br>
                        </p>

                        <h6>IF TRANSACTION NOT FOUND IN RECORD.</h6>

                        <pre><code class="language-json">
{
    "status": "fail",
    "message": "Transaction not found."
}</code></pre>
                        <strong> After successful CURL request example :</strong>
                        <p>If there will be no validation errors in request, response will be like:</p>

                        <pre><code class="language-json">
{
    "status": "success",
    "message" : "Refund Updated Successfully!"
}</code></pre>
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
