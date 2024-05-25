<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1.0, shrink-to-fit=no">
    <title>{{ config('app.name') }} | Merchant Crypto Pay API Document</title>
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
                            Crypto Payment API
                        </a>
                    </li>
                    <li class="nav-item"><a class="nav-link scrollto" href="#APIRequest">Request</a></li>
                    <li class="nav-item"><a class="nav-link scrollto" href="#APIResponse">Response</a></li>
                    <li class="nav-item"><a class="nav-link scrollto" href="#Webhooks">Webhooks</a></li>
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
                            <h1 class="docs-heading">Crypto API Documentation</h1>
                            <h4>How to integrate Crypto API</h4>
                            <section class="docs-intro">
                                <p>This is Crypto Page API documentation. In this API, User details will be
                                    sent to {{ env('APP_NAME') }} Server with curl request, where as card details will
                                    be
                                    loaded to {{ env('APP_NAME') }} server. Follow the below steps to integrate hosted
                                    page API with {{ env('APP_NAME') }}.</p>
                            </section>
                        </header>

                        <h4 class="mt-3">Using curl request</h4>
                        <p>The following parameters should be sent to the hosted page.</p>
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
                                        <td>first_name</td>
                                        <td>Yes</td>
                                        <td>String</td>
                                        <td>First Name</td>
                                    </tr>
                                    <tr>
                                        <td>last_name</td>
                                        <td>Yes</td>
                                        <td>String</td>
                                        <td>Last Name</td>
                                    </tr>
                                    <tr>
                                        <td>address</td>
                                        <td>Yes</td>
                                        <td>String</td>
                                        <td>Full Address of User</td>
                                    </tr>
                                    <tr>
                                        <td>country</td>
                                        <td>Yes</td>
                                        <td>String</td>
                                        <td>2 letter Country, eg US</td>
                                    </tr>
                                    <tr>
                                        <td>state</td>
                                        <td>Yes</td>
                                        <td>String</td>
                                        <td>State Name, 2 letter for US states, eg CA</td>
                                    </tr>
                                    <tr>
                                        <td>city</td>
                                        <td>Yes</td>
                                        <td>String</td>
                                        <td>Valid City name</td>
                                    </tr>
                                    <tr>
                                        <td>zip</td>
                                        <td>Yes</td>
                                        <td>String</td>
                                        <td>Valid Zip Code</td>
                                    </tr>
                                    <tr>
                                        <td>ip_address</td>
                                        <td>Yes</td>
                                        <td>String</td>
                                        <td>IP address of user device, eg 56.85.205.246</td>
                                    </tr>
                                    <tr>
                                        <td>email</td>
                                        <td>Yes</td>
                                        <td>String</td>
                                        <td>Valid Email address of User</td>
                                    </tr>
                                    <tr>
                                        <td>country_code</td>
                                        <td>Yes</td>
                                        <td>String</td>
                                        <td>Valid country code. (it's required for validate your phone_no)</td>
                                    </tr>
                                    <tr>
                                        <td>phone_no</td>
                                        <td>Yes</td>
                                        <td>String</td>
                                        <td>Valid Phone Number of User</td>
                                    </tr>
                                    <tr>
                                        <td>amount</td>
                                        <td>Yes</td>
                                        <td>Decimal</td>
                                        <td>Amount Value</td>
                                    </tr>
                                    <tr>
                                        <td>currency</td>
                                        <td>Yes</td>
                                        <td>String</td>
                                        <td>3 Digit format, eg USD</td>
                                    </tr>
                                    <tr>
                                        <td>customer_order_id</td>
                                        <td>No</td>
                                        <td>String</td>
                                        <td>Customer order id generated from user side.</td>
                                    </tr>
                                    <tr>
                                        <td>response_url</td>
                                        <td>Yes</td>
                                        <td>String</td>
                                        <td>Response URL where we redirect after transaction process completed.</td>
                                    </tr>
                                    <tr>
                                        <td>webhook_url</td>
                                        <td>No</td>
                                        <td>String</td>
                                        <td>POST URL where we send webhook notification.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3 mt-3">
                                <strong>Testing URL - </strong> <br><span
                                    class="text-danger-custom">{{ env('APP_URL') }}/api/test/crypto/transaction</span><br /><br />
                                <strong>Live URL - </strong> <br><span
                                    class="text-danger-custom">{{ env('APP_URL') }}/api/crypto/transaction</span>
                            </div>
                            <div class="col-md-12 mb-3">
                                <strong>Method - </strong> <code>POST</code>
                            </div>
                            <div class="col-md-12">
                                <strong>Send Curl request</strong>
                                <p>Send a Curl request with all the parameters provided to the URL below. We
                                    encourage all users to please provide all the necessary fields. All credit
                                    card fields in the request parameters must be provided and must be the same
                                    information indicated in the cardholder's credit card/billing information.
                                </p>
                            </div>
                        </div>

                    </section>
                    <!--//section-->

                    <section class="docs-section" id="APIResponse">
                        <h2 class="section-heading">Response</h2>
                        <p class="lead">After you redirect to url payment_redirect_url, a form will be loaded
                            over {{ env('APP_NAME') }} payment page. You will need to fill credit card details if
                            asked</p>

                        <h4>Validation Errors</h4>
                        <p class="lead">If in case there will be validation errors in the request, the response
                            will be similar to the following:</p>
                        <pre> <code class="language-json"> {
    "status": "fail",
    "message": "Some parameters are missing or invalid request data, please check 'errors' parameter for more details.",
    "errors": {
        "first_name": [
            "The first name field is required."
        ]
    },
    "data": {
        "order_id": null,
        "amount": "5",
        "currency": "USD",
        "email": "example@mail.com",
        "customer_order_id": null
    }
}</code></pre>
                        <h4>Successful Response</h4>
                        <p class="lead">After a successful request, the response will be returned in JSON
                            format.</p>
                        <strong>Example</strong>
                        <pre><code class="language-json">{
    "status": "3d_redirect",
    "message": "3DS link generated successfully, please redirect to 'redirect_3ds_url'.",
    "redirect_3ds_url": "{{ env('APP_URL') }}/hosted-pay/input-card/8JL81624975054",
    "customer_order_id": null,
    "api_key": "your_api_key"
}</code></pre>
                        <p>If the request data contains a valid format, then the above response will be
                            returned. You will need to redirect to “payment_redirect_url” for payment page
                            before “valid_till”.</p>
                        <h4>Response from {{ env('APP_NAME') }}</h4>
                        <p class="lead">
                            After Credit card form is completely filled up, the user must press the Pay button.
                            This request will take some time, if user card has 3D secure feature enabled, it
                            will also redirect the process to a 3D secure page, where user will be asked to
                            input PIN or OTP if asked. After the entire process is complete, user will be
                            redirected to the merchant website and will reflect the transaction status.
                        </p>
                        <p>
                            If transaction is successful, the user will be redirected to ”response_url” with the
                            response in query string like the one below:
                        </p>
                        <pre class="pre"><code class="language-text">{{ config('app.url') }}/success?status=success&message=Your%20transaction%20was%20success&order_id=20190000458521&customer_order_id=456789521365">{{ config('app.url') }}/success?status=success&message=Your%20transaction%20was%20success&order_id=20190000458521&customer_order_id=456789521365</code></pre>
                        If the transaction fails, the user will redirect as well to “response_url” with the
                        response query string similar to the one below:
                        <pre class="pre"><code class="language-text">{{ config('app.url') }}/fail?status=fail&message=Activity%20limit%20exceeded.&order_id=20190000458521&customer_order_id=456789521365">{{ config('app.url') }}/fail?status=fail&message=Activity%20limit%20exceeded.&order_id=20190000458521&customer_order_id=456789521365</code></pre>
                    </section>
                    <!--//section-->

                    <section class="docs-section" id="Webhooks">
                        <h2 class="section-heading">Webhooks</h2>
                        <p class="lead">Webhooks events are transaction callbacks that sends notifications of
                            transaction to the merchant server. If you want to receive webhooks, then send
                            "webhook_url" parameter with initial request(See above request example).</p>

                        <p>Here are the simple explanation of each parameter:</p>

                        <pre><code class="language-json">1.) order_id : Transaction reference number of our system.
2.) customer_order_id: Merchant transaction reference.
3.) transaction_status: "success" / "fail" / "pending" / "blocked".
4.) reason: Response from the bank about transaction status.
5.) currency: Currency of the transaction.
6.) amount: Amount of the transaction.
7.) email: Email of the transaction.
8.) transaction_date: Date of the transaction.</code></pre>
                        <strong> Webhook Example</strong>

                        <p>Here are the example of webhook notification request:</p>
                        <pre><code class="language-json">{
  "responseCode": "1",
  "responseMessage": "Transaction processed successfully.",
  "data": {
    "transaction": {
      "order_id": "TRNU4US1707138988242FZZ",
      "customer_order_id": null,
      "amount": "11",
      "currency": "USD"
    },
    "client": {
      "first_name": "Test",
      "last_name": "{{ config('app.name') }}",
      "email": "tech@testpay.com",
      "phone_no": "8676767676",
      "address": "Testing address",
      "zip": "676776",
      "city": "Test",
      "state": "Test",
      "country": "Test"
    }
  }
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
