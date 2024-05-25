<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1.0, shrink-to-fit=no">
    <title>{{ config('app.name') }} | Hosted API v2 Document</title>
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
                        <a class="nav-link scrollto active" href="#APIRequestIn1">
                            <span class="theme-icon-holder me-2">
                                <i class="fa fa-file-code-o"></i>
                            </span>
                            Hosted Payment API V2
                        </a>
                    </li>
                    <li class="nav-item"><a class="nav-link scrollto" href="#APIRequestIn1">Prerequisite</a></li>
                </ul>

                <ul class="section-items list-unstyled nav flex-column pb-3">
                    <li class="nav-item section-title">
                        <a class="nav-link scrollto active" href="#APIRequestIn2">
                            <span class="theme-icon-holder me-2">
                                <i class="fa fa-file-code-o"></i>
                            </span>
                            Create Test Transaction
                        </a>
                    </li>
                    <li class="nav-item"><a class="nav-link scrollto" href="#APIRequest2">Request</a></li>
                    <li class="nav-item"><a class="nav-link scrollto" href="#APIResponse2">Response</a></li>
                    <li class="nav-item"><a class="nav-link scrollto" href="#APIRedirection2">Redirection</a></li>
                </ul>

                <ul class="section-items list-unstyled nav flex-column pb-3">
                    <li class="nav-item section-title">
                        <a class="nav-link scrollto active" href="#APIRequestIn3">
                            <span class="theme-icon-holder me-2">
                                <i class="fa fa-file-code-o"></i>
                            </span>
                            Create Transaction
                        </a>
                    </li>
                    <li class="nav-item"><a class="nav-link scrollto" href="#APIRequest3">Request</a></li>
                    <li class="nav-item"><a class="nav-link scrollto" href="#APIResponse3">Response</a></li>
                    <li class="nav-item"><a class="nav-link scrollto" href="#APIRedirection3">Redirection</a></li>
                </ul>
            </nav>
            <!--//docs-nav-->
        </div>
        <!--//docs-sidebar-->
        <div class="docs-content">
            <div class="container">
                <article class="docs-article" id="APIRequestIn1">
                    <header class="docs-header">
                        <h1 class="docs-heading">Hosted Payment API V2</h1>
                        <h4>Prerequisite</h4>
                        <section class="docs-intro">
                            <p>Before implementing API V2 you need to follow below steps.</p>
                        </section>

                        <ul>
                            <li>All server IPs must be whitelisted. <a href="{{ env('APP_URL') }}/whitelist-ip"
                                    class="text-danger-custom" target="_blank"><i
                                        class="fa fa-external-link-alt"></i>
                                    Whitelist Ips</a></li>

                            <li>Also, make sure that <strong>webhook_url</strong> is not secured by any type of
                                <strong>token (e.g. Bearer, JWT, Oauth2)</strong>, and will be able to get
                                external
                                requests from servers.
                            </li>
                        </ul>


                        <h4>How to integrate API V2</h4>
                        <p class="lead">This API can be implemented with all of the platforms. For that, send
                            a
                            <strong>request</strong> with
                            <strong>payload</strong> in
                            <strong>JSON</strong> format to our API Endpoint.
                        </p>
                    </header>
                </article>

                <article class="docs-article" id="APIRequestIn2">
                    <header class="docs-header">
                        <h1 class="docs-heading">Create Test Transaction</h1>
                        <p class="lead">To make <strong>test transactions</strong> you need to make a
                            <strong>POST</strong> request to the following url :
                        </p>
                        <span class="badge badge-primary">{{ env('APP_URL') }}/api/v2/test/transaction</span>
                    </header>
                    <section class="docs-section" id="APIRequest2">
                        <section class="docs-section" id="authorization">
                            <h2 class="section-heading">Authorization</h2>
                            <p>To authenticate the user we have to pass <code>API-KEY</code> in our request
                                header. Please find below how we can pass API-KEY in header during request.</p>
                            <pre><code class="language-text">Authorization: Bearer 1|3WIXuhS9eYVh1vFjG8JoyfztPTqeE9EVXyeJS77676</code></pre>
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
        }
    }
}</code></pre>
                        </section>
                        <h4>Request Parameters *</h4>
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
                                        <td>first_name</td>
                                        <td>Yes</td>
                                        <td>String</td>
                                        <td>First Name from (Credit/Debit) Card</td>
                                    </tr>
                                    <tr>
                                        <td>last_name</td>
                                        <td>Yes</td>
                                        <td>String</td>
                                        <td>Last Name from (Credit/Debit)Card</td>
                                    </tr>
                                    <tr>
                                        <td>email</td>
                                        <td>Yes</td>
                                        <td>String</td>
                                        <td>Valid Email address of User</td>
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
                                        <td>ip_address</td>
                                        <td>Yes</td>
                                        <td>String</td>
                                        <td>IP address of user device, eg 56.85.205.246</td>
                                    </tr>
                                    <tr>
                                        <td>address</td>
                                        <td>No</td>
                                        <td>String</td>
                                        <td>Full Address of User</td>
                                    </tr>
                                    <tr>
                                        <td>country</td>
                                        <td>No</td>
                                        <td>String</td>
                                        <td>2 letter Country, eg US</td>
                                    </tr>
                                    <tr>
                                        <td>state</td>
                                        <td>No</td>
                                        <td>String</td>
                                        <td>State Name, 2 letter for US states, eg CA</td>
                                    </tr>
                                    <tr>
                                        <td>city</td>
                                        <td>No</td>
                                        <td>String</td>
                                        <td>Valid City name</td>
                                    </tr>
                                    <tr>
                                        <td>zip</td>
                                        <td>No</td>
                                        <td>String</td>
                                        <td>Valid Zip Code</td>
                                    </tr>

                                    <tr>
                                        <td>phone_no</td>
                                        <td>No</td>
                                        <td>String</td>
                                        <td>Valid Phone Number of User</td>
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
                                        <td>NO</td>
                                        <td>String</td>
                                        <td>POST URL where we send webhook notification.</td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <strong> Non-3DS Testing card data - </strong>
                                <pre><code class="language-json">card_no : 4242 4242 4242 4242
ccExpiryMonth : 02
ccExpiryYear : 2026
cvvNumber : 123</code></pre>
                            </div>
                            <div class="col-md-6">
                                <strong> 3DS Testing card data - </strong>
                                <pre><code class="language-json">card_no : 4000 0000 0000 3220
ccExpiryMonth : 02
ccExpiryYear : 2026
cvvNumber : 123</code></pre>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <strong class="text-danger-custom">Note :</strong> <strong>Non required</strong>
                                parameters
                                are not necessary for
                                this request. But you may need those parameters in further transaction process.
                                Which depends on upon <strong>Gateway</strong>. Filling non required parameters
                                in this
                                request will fill
                                corresponding fields in further process automatically and won't need to be
                                filled
                                again.
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <strong class="badge badge-primary">API Endpoint - </strong> <br><span
                                    class="">{{ env('APP_URL') }}/api/v2/transaction</span><br />
                            </div>
                            <div class="col-md-12 mb-3">
                                <strong class="badge badge-primary">Test API Endpoint - </strong> <br><span
                                    class="">{{ env('APP_URL') }}/api/v2/test/transaction</span><br />
                            </div>
                            <div class="col-md-12 mb-3">
                                <strong>Method - </strong> <span class="badge bg__primary">POST</span>
                            </div>
                            <div class="col-md-12">
                                <strong>API Request (curl)</strong>
                                <pre><code class="language-php">$url = "{{ env('APP_URL') }}/api/v2/test/transaction";

$data = [
    'first_name' => 'First Name',
    'last_name' => 'Last Name',
    'email' => 'test@gmail.com',
    'amount' => '10.00',
    'currency' => 'USD',
    'response_url' => 'https://yourdomain.com/callback.php',
    'address' => 'Address',
    'country' => 'US',
    'state' => 'NY',
    'city' => 'New York',
    'zip' => '38564',
    'phone_no' => '999999999',
    'ip_address' => '56.85.205.246'
];

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_HTTPHEADER,[
    'Content-Type: application/json',
    'Authorization: Bearer 1|J3WoPkRDtCRQzyoYUwBtdYGG9JlBN0HwYZ66656'
]);
$response = curl_exec($curl);
curl_close($curl);

$responseData = json_decode($response);

if(isset($responseData['responseCode']) && $respsonseData['responseCode'] == '7') {
    header("Location: ".$responseData['3dsUrl']);
} elseif(isset($responseData['responseCode']) && $respsonseData['responseCode'] == '1') {
    echo "your transaction was approved.";
    print_r($responseData);
} else {
    echo "your transaction was declined";
    print_r($responseData);
}</code></pre>
                            </div>
                        </div>
                    </section>
                    <section class="docs-section" id="APIResponse2">
                        <header class="docs-header">
                            <h4>Response</h4>
                            <section class="docs-intro">
                                <p>After a successful CURL request, the response will be sent in JSON
                                    format.</p>
                            </section>
                        </header>
                        <h4 class="mt-3">Validation Errors</h4>
                        <p class="lead">Validation errors in request will produce response like:</p>
                        <pre><code class="language-json">{
    "responseCode": "6",
    "responseMessage": "The first name field is required.",
    "data": {
        "transaction": {
            "order_id": null,
            "customer_order_id": null,
            "amount": "1",
            "currency": "USD"
        },
        "client": {
            "first_name": null,
            "last_name": "Test",
            "email": "test@gmail.com",
            "phone_no": null,
            "address": null,
            "zip": null,
            "city": null,
            "state": null,
            "country": "IN"
        }
    }
}</code></pre>
                        <h4>Successful 3Ds secure Response</h4>
                        <p class="lead">If the response contains <strong>"status":"3d_redirect"</strong> then
                            you
                            need to redirect the your request to
                            <strong>"redirect_3ds_url"</strong>. Which will be the merchant website.
                        </p>
                        <pre><code class="language-json">{
    "responseCode": "7",
    "responseMessage": "Please redirect to 3dsUrl.",
    "3dsUrl": "https://testpay.com/api/v2/test-checkout/TRNU4US1707138988242FZZ",
    "data": {
        "transaction": {
            "order_id": "TRNU4US1707138988242FZZ",
            "customer_order_id": null,
            "amount": "1",
            "currency": "USD"
        },
        "client": {
            "first_name": "{{ config('app.name') }}",
            "last_name": "Test",
            "email": "test@gmail.com",
            "phone_no": null,
            "address": null,
            "zip": null,
            "city": null,
            "state": null,
            "country": "IN"
        }
    }
}</code></pre>
                        <section>
                            <h2 class="section-heading">Response Codes</h2>
                            <h6>These are the response code with their meanings:</h6>
                            <pre><code class="language-json">1.) "0" : "Fail" => It means transaction get declined. 
2.) "1" :"Success" => It means transaction processed successfully.
3.) "2" :"Pending" => It means user not complete the transaction process or there is some other issue that's why transaction got stuck in between.
4.) "3" :"Cancelled" => It means user cancelled the transaction.
5.) "4" :"to_be_confirm" => It means when there is some delay in bank response to confirm the transaction status.
6.) "5" :"Blocked" => It means when transaction blocked due to restricted country or per day transaction or card limit.
7.) "6" :"Unathorized" => It means when user pass wrong API-KEY.
8.) "7" :"Redirected" => It means redirect user to 3ds link to complete the transaction.
</code> </pre>
                        </section>
                        <section class="docs-section" id="APIRedirection2">
                            <header class="docs-header">
                                <h4>Redirection</h4>
                                <section class="docs-intro">
                                    <p>After successful response of the request, you will be redirected to
                                        the
                                        merchant website.</p>
                                </section>
                            </header>

                            <div class="image-popup">
                                <img src="{{ storage_asset('NewTheme/images/documentation/test-select-payment-method.png') }}"
                                    alt="" class="img-fluid img-thumbnail no-gutters document-image"
                                    style="cursor: pointer;">
                            </div>

                            <p class="mt-3">From there, you can choose <strong>Card</strong>,
                                <strong>Bank</strong>
                                or
                                <strong>Crypto Currency</strong> to create your transaction.
                            </p>


                            <p class="mt-3"><strong>Pay With Card</strong></p>
                            <div class="image-popup">
                                <img src="{{ storage_asset('NewTheme/images/documentation/Pay_with_card.png') }}"
                                    alt="" class="img-fluid img-thumbnail no-gutters document-image"
                                    style="cursor: pointer;">
                            </div>

                            <p class="mt-3"><strong>Pay With Bank</strong></p>
                            <div class="image-popup">
                                <img src="{{ storage_asset('NewTheme/images/documentation/Pay_with_bank.png') }}"
                                    alt="" class="img-fluid img-thumbnail no-gutters document-image"
                                    style="cursor: pointer;">
                            </div>

                            <p class="mt-3"><strong>Pay With Crypto Wallet</strong></p>
                            <div class="image-popup">
                                <img src="{{ storage_asset('NewTheme/images/documentation/Pay_with_crypto.png') }}"
                                    alt="" class="img-fluid img-thumbnail no-gutters document-image"
                                    style="cursor: pointer;">
                            </div>

                            <p class="lead">Also, you can cancel and retry another method of transaction after
                                selecting
                                any of above method.</p>
                            <div class="image-popup">
                                <img src="{{ storage_asset('NewTheme/images/documentation/Payment_cancel.png') }}"
                                    alt="" class="img-fluid img-thumbnail no-gutters document-image"
                                    style="cursor: pointer;">
                            </div>
                        </section>
                </article>

                <article class="docs-article" id="APIRequestIn3">
                    <header class="docs-header">
                        <h1 class="docs-heading">Create Transaction</h1>
                        <p class="lead">To make <strong>transactions</strong> you need to make a
                            <strong>POST</strong> request to the following url :
                        </p>
                        <span class="badge badge-primary">{{ env('APP_URL') }}/api/v2/transaction</span>
                    </header>
                    <section class="docs-section" id="APIRequest3">
                        <section class="docs-section">
                            <h2 class="section-heading">Authorization</h2>
                            <p>To authenticate the user we have to pass <code>API-KEY</code> in our request
                                header. Please find below how we can pass API-KEY in header during request.</p>
                            <pre><code class="language-text">Authorization: Bearer 1|3WIXuhS9eYVh1vFjG8JoyfztPTqeE9EVXyeJS77676</code></pre>
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
        }
    }
}</code></pre>
                        </section>
                        <h4>Request Parameters *</h4>
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
                                        <td>first_name</td>
                                        <td>Yes</td>
                                        <td>String</td>
                                        <td>First Name from (Credit/Debit) Card</td>
                                    </tr>
                                    <tr>
                                        <td>last_name</td>
                                        <td>Yes</td>
                                        <td>String</td>
                                        <td>Last Name from (Credit/Debit)Card</td>
                                    </tr>
                                    <tr>
                                        <td>email</td>
                                        <td>Yes</td>
                                        <td>String</td>
                                        <td>Valid Email address of User</td>
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
                                        <td>ip_address</td>
                                        <td>Yes</td>
                                        <td>String</td>
                                        <td>IP address of user device, eg 56.85.205.246</td>
                                    </tr>
                                    <tr>
                                        <td>response_url</td>
                                        <td>Yes</td>
                                        <td>String</td>
                                        <td>Response URL where we redirect after transaction process completed.</td>
                                    </tr>
                                    <tr>
                                        <td>address</td>
                                        <td>No</td>
                                        <td>String</td>
                                        <td>Full Address of User</td>
                                    </tr>
                                    <tr>
                                        <td>country</td>
                                        <td>No</td>
                                        <td>String</td>
                                        <td>2 letter Country, eg US</td>
                                    </tr>
                                    <tr>
                                        <td>state</td>
                                        <td>No</td>
                                        <td>String</td>
                                        <td>State Name, 2 letter for US states, eg CA</td>
                                    </tr>
                                    <tr>
                                        <td>city</td>
                                        <td>No</td>
                                        <td>String</td>
                                        <td>Valid City name</td>
                                    </tr>
                                    <tr>
                                        <td>zip</td>
                                        <td>No</td>
                                        <td>String</td>
                                        <td>Valid Zip Code</td>
                                    </tr>

                                    <tr>
                                        <td>phone_no</td>
                                        <td>No</td>
                                        <td>String</td>
                                        <td>Valid Phone Number of User</td>
                                    </tr>

                                    <tr>
                                        <td>webhook_url</td>
                                        <td>No</td>
                                        <td>String</td>
                                        <td>Valid Webhook URL</td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <strong class="text-danger-custom">Note :</strong> <strong>Non required</strong>
                                parameters
                                are not necessary for
                                this request. But you may need those parameters in further transaction process.
                                Which depends on upon <strong>Gateway</strong>. Filling non required parameters
                                in this
                                request will fill
                                corresponding fields in further process automatically and won't need to be
                                filled
                                again.
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <strong class="badge badge-primary">API Endpoint - </strong> <br><span
                                    class="">{{ env('APP_URL') }}/api/v2/transaction</span><br />
                            </div>
                            <div class="col-md-12 mb-3">
                                <strong>Method - </strong> <span class="badge bg__primary">POST</span>
                            </div>
                            <div class="col-md-12">
                                <strong>API Request (curl)</strong>
                                <pre><code class="language-php">$url = "{{ env('APP_URL') }}/api/v2/transaction";


$data = [
    'first_name' => 'First Name',
    'last_name' => 'Last Name',
    'email' => 'test@gmail.com',
    'amount' => '10.00',
    'currency' => 'USD',
    'response_url' => 'https://yourdomain.com/callback.php',
    'address' => 'Address',
    'country' => 'US',
    'state' => 'NY',
    'city' => 'New York',
    'zip' => '38564',
    'phone_no' => '999999999'
    'webhook_url' => 'https://yourdomain.com/webhook.php',
    'ip_address' => '56.85.205.246'
];

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_HTTPHEADER,[
    'Content-Type: application/json',
    'Authorization: Bearer 1|J3WoPkRDtCRQzyoYUwBtdYGG9JlBN0HwYZ66656'
]);
$response = curl_exec($curl);
curl_close($curl);

$responseData = json_decode($response);

if(isset($responseData['responseCode']) && $respsonseData['responseCode'] == '7') {
    header("Location: ".$responseData['redirect_3ds_url']);
} elseif(isset($responseData['responseCode']) && $respsonseData['responseCode'] == '1') {
    echo "your transaction was approved.";
    print_r($responseData);
} else {
    echo "your transaction was declined";
    print_r($responseData);
}</code></pre>
                            </div>
                        </div>
                    </section>
                    <section class="docs-section" id="APIResponse3">
                        <header class="docs-header">
                            <h4>Response</h4>
                            <section class="docs-intro">
                                <p>After a successful CURL request, the response will be sent in JSON
                                    format.</p>
                            </section>
                        </header>
                        <h4 class="mt-3">Validation Errors</h4>
                        <p class="lead">Validation errors in request will produce response like:</p>
                        <pre><code class="language-json">{
    "responseCode": "6",
    "responseMessage": "The first name field is required.",
    "data": {
        "transaction": {
            "order_id": null,
            "customer_order_id": null,
            "amount": "1",
            "currency": "USD"
        },
        "client": {
            "first_name": null,
            "last_name": "Test",
            "email": "test@gmail.com",
            "phone_no": null,
            "address": null,
            "zip": null,
            "city": null,
            "state": null,
            "country": "IN"
        }
    }
}</code></pre>
                        <h4>Successful 3Ds secure Response</h4>
                        <p class="lead">If the response contains <strong>"status":"3d_redirect"</strong> then
                            you
                            need to redirect the your request to
                            <strong>"redirect_3ds_url"</strong>. Which will be the merchant website.
                        </p>
                        <pre><code class="language-json">{
    "responseCode": "7",
    "responseMessage": "Please redirect to 3dsUrl.",
    "3dsUrl": "https://testpay.com/api/v2/test-checkout/TRNU4US1707138988242FZZ",
    "data": {
        "transaction": {
            "order_id": "TRNU4US1707138988242FZZ",
            "customer_order_id": null,
            "amount": "1",
            "currency": "USD"
        },
        "client": {
            "first_name": "{{ config('app.name') }}",
            "last_name": "Test",
            "email": "test@gmail.com",
            "phone_no": null,
            "address": null,
            "zip": null,
            "city": null,
            "state": null,
            "country": "IN"
        }
    }
}
</code></pre>
                    </section>
                    <section>
                        <h2 class="section-heading">Response Codes</h2>
                        <h6>These are the response code with their meanings:</h6>
                        <pre><code class="language-json">1.) "0" : "Fail" => It means transaction get declined. 
2.) "1" :"Success" => It means transaction processed successfully.
3.) "2" :"Pending" => It means user not complete the transaction process or there is some other issue that's why transaction got stuck in between.
4.) "3" :"Cancelled" => It means user cancelled the transaction.
5.) "4" :"to_be_confirm" => It means when there is some delay in bank response to confirm the transaction status.
6.) "5" :"Blocked" => It means when transaction blocked due to restricted country or per day transaction or card limit.
7.) "6" :"Unathorized" => It means when user pass wrong API-KEY.
8.) "7" :"Redirected" => It means redirect user to 3ds link to complete the transaction.
</code> </pre>

                        <strong style="font-size: 25px;"> Webhook Example</strong>

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
      "phone_no": "787878778",
      "address": "Testing address",
      "zip": "676776",
      "city": "Test",
      "state": "Test",
      "country": "Test"
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
                    <section class="docs-section" id="APIRedirection3">
                        <header class="docs-header">
                            <h4>Redirection</h4>
                            <section class="docs-intro">
                                <p>After successful response of the request, you will be redirected to
                                    the
                                    merchant website.</p>
                            </section>
                        </header>
                    </section>
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
