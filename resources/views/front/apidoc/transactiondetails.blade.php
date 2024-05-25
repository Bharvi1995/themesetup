<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1.0, shrink-to-fit=no">
    <title>{{ config('app.name') }} | Merchant Get Transaction Details API Document</title>
    <link rel="icon" type="image/png" sizes="16x16" href="{{ storage_asset('NewTheme/images/favicon.ico') }}">
    <link rel="shortcut icon" href="{{ storage_asset('NewTheme/images/favicon.ico') }}" />

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&family=Roboto:wght@100;300;400;500;700;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css"
        integrity="sha512-5A8nwdMOWrSz20fDsjczgUidUBR8liPYU+WymTZP1lmY9G6Oc7HlZv156XqnsgNUzTyMefFTcsFH/tnJE/+xBg=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer" />

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
                            Get Transaction Details API
                        </a>
                    </li>
                    <li class="nav-item"><a class="nav-link scrollto" href="#authorization">Authorization</a></li>
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
                            <h1 class="docs-heading">Get Transaction Details API</h1>
                            <p class="lead">To Confirm If Your Transaction Was Success Or Declined, You Can Call
                                Bellow New API.</p>
                            <p class="lead">This API Call Is Optional, It Is Just For Security And Transaction
                                Confirmation. You Do Not Need To Call This API To Complete Transaction.</p>
                        </header>
                        <section class="docs-section" id="authorization">
                            <h2 class="section-heading">Authorization</h2>
                            <p>To authenticate the user we have to pass <code>API-KEY</code> in our request
                                header. Please find below how we can pass API-KEY in header during request.</p>
                            <pre>Authorization: Bearer 1|3WIXuhS9eYVh1vFjG8JoyfztPTqeE9EVXyeJS77676</pre>
                            <p>If we do not pass <code>API-KEY</code> in header then we will get this error
                                in our response.</p>
                            <pre><code class="language-json">{
    "responseCode": "6",
    "responseMessage": "Unauthorised request, please pass API Key in Header",
    "data": {
        "transaction": {
            "order_id": null,
            "customer_order_id": "xr4u170713hp",
            "amount": "12.50",
            "currency": "USD"
        },
        "client": {
            "first_name": "Test",
            "last_name": "Test",
            "email": "test@gmail.com",
            "phone_no": "+4477676767",
            "address": "Test",
            "zip": "123456",
            "city": "Test",
            "state": "Test",
            "country": "GB"
        },
        "card": {
            "card_no": "424242XXXXXX4243",
            "ccExpiryMonth": "05",
            "ccExpiryYear": "2023",
            "cvvNumber": "XXX"
        }
    }
}</code></pre>
                        </section>

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
                                    class="text-danger-custom">{{ env('APP_URL') }}/api/get/transaction</span>
                            </div>
                            <div class="col-md-12 mb-3">
                                <strong>Method - </strong> <code>POST</code>
                            </div>
                            <div class="col-md-12">
                                <p><strong>Note:</strong> <code>order_id</code> or
                                    <code>customer_order_id</code> One Parameter Should Be Pass. Both Parameter
                                    Can Be Pass, But At Least One Is Required.
                                </p>
                            </div>
                        </div>

                    </section>
                    <!--//section-->

                    <section class="docs-section" id="APIResponse">
                        <h2 class="section-heading">Response Codes</h2>
                        <p class="lead">After a successful CURL request, the response code will be sent in
                            JSON
                            format.</p>

                        <h6>These are the response code with their meanings:</h6>
                        <pre><code class="language-json">1.) "0" : "Fail" => It means transaction get declined. 
2.) "1" :"Success" => It means transaction processed successfully.
3.) "2" :"Pending" => It means user not complete the transaction process or there is some other issue that's why transaction got stuck in between.
4.) "3" :"Cancelled" => It means user cancelled the transaction.
5.) "4" :"to_be_confirm" => It means when there is some delay in bank response to confirm the transaction status.
6.) "5" :"Blocked" => It means when transaction blocked due to restricted country or per day transaction or card limit.
7.) "6" :"Unathorized" => It means when user pass wrong API-KEY or wrong order_id.
8.) "7" :"Redirected" => It means redirect user to 3ds link to complete the transaction.
</code></pre>

                        <h6>IF TRANSACTION NOT FOUND IN RECORD.</h6>

                        <pre><code class="language-json">{
    "responseCode": "6",
    "responseMessage": "Not found."
}</code></pre>
                        <strong> After successful CURL request example :</strong>
                        <p>If there will be no validation errors in request, response will be like:</p>

                        <pre><code class="language-json">{
    "responseCode": "1",
    "responseMessage": "Your transaction has been processed successfully.",
    "data": {
        "transaction": {
            "order_id": "1681120036EW1ZKPM259",
            "customer_order_id": null,
            "amount": "5.00",
            "currency": "USD"
        },
        "client": {
            "first_name": "{{ config('app.name') }}",
            "last_name": "Test",
            "email": "test@gmail.com",
            "phone_no": "887123456",
            "address": "suit 1 ,a.l.evelyn ltd building, charlestown , nevis",
            "zip": "30001",
            "city": "London",
            "state": "London",
            "country": "GB"
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
