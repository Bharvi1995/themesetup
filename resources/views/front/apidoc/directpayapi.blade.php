<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1.0, shrink-to-fit=no">
    <title>{{ config('app.name') }} | Merchant Direct API Document</title>
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
        h1,
        .h1,
        h2,
        .h2,
        h3,
        .h3,
        h4,
        .h4,
        h5,
        .h5,
        h6,
        .h6 {
            color: #000000 !important;
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
                            Direct Payment API
                        </a>
                    </li>
                    <li class="nav-item"><a class="nav-link scrollto" href="#authorization">Authorization</a></li>
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
                    <header class="docs-header">
                        <h1 class="docs-heading">Direct API Documentation</h1>
                        <h4>How to integrate Direct (Server to Server) API</h4>
                        <section class="docs-intro">
                            <p>Our payment API can be used to integrate with any platform of your choice.</p>
                        </section>
                    </header>
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
                    <!--//section-->

                    <section class="docs-section" id="APIRequest">
                        <h2 class="section-heading">Request</h2>
                        <h4>Request Parameter *</h4>
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
                                        <td>No</td>
                                        <td>String</td>
                                        <td>Valid Country Code of User</td>
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
                                        <td>card_no</td>
                                        <td>Yes</td>
                                        <td>String</td>
                                        <td>Credit Card number</td>
                                    </tr>
                                    <tr>
                                        <td>ccExpiryMonth</td>
                                        <td>Yes</td>
                                        <td>String</td>
                                        <td>Credit card 2 digit expiry month, E.g. 04</td>
                                    </tr>
                                    <tr>
                                        <td>ccExpiryYear</td>
                                        <td>Yes</td>
                                        <td>String</td>
                                        <td>Credit card 4 digit expiry Year, E.g. 2022</td>
                                    </tr>
                                    <tr>
                                        <td>cvvNumber</td>
                                        <td>Yes</td>
                                        <td>String</td>
                                        <td>Credit card CVV number</td>
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
                            <div class="col-md-12 mb-3 mt-3">
                                <strong>Testing URL - </strong> <br><span
                                    class="text-danger-custom">{{ env('APP_URL') }}/api/test/transaction</span><br /><br />
                                <strong>Live URL - </strong> <br><span
                                    class="text-danger-custom">{{ env('APP_URL') }}/api/transaction</span>
                            </div>
                            <div class="col-md-12 mb-3">
                                <strong>Method - </strong> <code>POST</code>
                            </div>
                            <div class="col-md-12">
                                <strong>API Call Example</strong>
                                <pre><code class="language-php">// You can call our API following curl post example
$url = "{{ env('APP_URL') }}/api/transaction";
$key = "Your API Key";
// Fill with real customer info
$data = [
    'first_name' => 'First Name',
    'last_name' => 'Last Name',
    'address' => 'Address',
    'customer_order_id' => 'ORDER-78544646461235',
    'country' => 'US',
    'state' => 'NY', // if your country US then use only 2 letter state code.
    'city' => 'New York',
    'zip' => '38564',
    'ip_address' => '192.168.168.4',
    'email' => 'test@gmail.com',
    'country_code' => '+91',
    'phone_no' => '999999999',
    'amount' => '10.00',
    'currency' => 'USD',
    'card_no' => '4242424242424242',
    'ccExpiryMonth' => '02',
    'ccExpiryYear' => '2026',
    'cvvNumber' => '123',
    'response_url' => 'https://yourdomain.com/callback.php',
    'webhook_url' => 'https://yourdomain.com/notification.php',
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

if(isset($responseData['status']) && $respsonseData['status'] == '3d_redirect') {
    header("Location: ".$responseData['redirect_3ds_url']);
} elseif(isset($responseData['status']) && $respsonseData['status'] == 'success') {
    echo "your transaction was approved.";
    print_r($responseData);
} else {
    echo "your transaction was declined";
    print_r($responseData);                    
}</code></pre>
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
7.) "6" :"Unathorized" => It means when user pass wrong API-KEY.
8.) "7" :"Redirected" => It means redirect user to 3ds link to complete the transaction.
</code> </pre>

                        <h4>Validation Errors</h4>
                        <p class="lead">If in case of validation errors in request, response will be like:
                        </p>
                        <pre><code class="language-json">
{
    "responseCode": "6",
    "responseMessage": "The first name field is required.",
    "data": {
        "transaction": {
            "order_id": null,
            "customer_order_id": "xr4u170713hp",
            "amount": "12.50",
            "currency": "USD"
        },
        "client": {
            "first_name": null,
            "last_name": "Test",
            "email": "test@gmail.com",
            "phone_no": "+4478777877",
            "address": "Test",
            "zip": "123456",
            "city": "Test",
            "state": "Test",
            "country": "GB"
        }
    }
}
</code></pre>

                        <h4>Successful Response</h4>
                        <p class="lead">This is successful transaction response</p>
                        <pre><code class="language-json">{
    "responseCode": "1",
    "responseMessage": "Transaction processed successfully.",
    "data": {
        "transaction": {
            "order_id": "TRNU4US1707138988242FZZ",
            "customer_order_id": "xr4u170713hp",
            "amount": "12.50",
            "currency": "USD"
        },
        "client": {
            "first_name": "Test",
            "last_name": "Test",
            "email": "test@gmail.com",
            "phone_no": "+44898989898",
            "address": "Test",
            "zip": "123456",
            "city": "Test",
            "state": "Test",
            "country": "GB"
        }
    }
}
</code></pre>
                        <h4>Declined Response</h4>
                        <p class="lead">This is the declined transaction response</p>
                        <pre><code class="language-json">{
    "responseCode": "0",
    "responseMessage": "Card not supported for testing.",
    "data": {
        "transaction": {
            "order_id": "TRNU4US1707138988242FZZ",
            "customer_order_id": "xr4u170713hp",
            "amount": "12.50",
            "currency": "USD"
        },
        "client": {
            "first_name": "Test",
            "last_name": "Test",
            "email": "test@gmail.com",
            "phone_no": "+44898989898",
            "address": "Test",
            "zip": "123456",
            "city": "Test",
            "state": "Test",
            "country": "GB"
        }
    }
}</code></pre>

                        <h4>3Ds secure Json Response type</h4>
                        <p class="lead">If the response returns “status”:"3d_redirect” need to redirect the
                            “redirect_3ds_url”</p>

                        <h6>
                            Response After 3DS
                        </h6>
                        <p>After 3D secure is completed, the user will be redirected to merchant website.</p>

                        <p>If transaction will be successful, the user will redirect to ”response_url” with
                            response in query string similar to the one below:</p>
                        <pre><code class="language-text">{{ config('app.url') }}?responseCode=1&responseMessage=Test%20transaction%20processed%20successfully.&order_id=TRNU4US1707138988242FZZ&customer_order_id=xr4u170713hp</code></pre>

                        <p>If the transaction fails, the user will be redirected to “response_url” with response
                            in query string as follows:</p>

                        <pre><code class="language-text">{{ config('app.url') }}?responseCode=0&responseMessage=Card%not%supported%for%testing.&order_id=TRNU4US1707138988242FZZ&customer_order_id=xr4u170713hp</code></pre>
                        <pre><code class="language-json">{
    "responseCode": "7",
    "responseMessage": "Please redirect to 3dsUrl.",
    "3dsUrl": "{{ config('app.url') }}/payment/test-transaction/SDZSOO1683713932",
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
            "phone_no": "+44898989898",
            "address": "Test",
            "zip": "123456",
            "city": "Test",
            "state": "Test",
            "country": "GB"
        }
    }
}</code></pre>
                    </section>
                    <!--//section-->

                    <section class="docs-section" id="Webhooks">
                        <h2 class="section-heading">Webhooks</h2>
                        <p class="lead">Webhooks events are transaction callbacks that sends notifications of
                            transaction to the merchant server. If you want to receive webhooks, then send
                            "webhook_url" parameter with initial request(See above request example).</p>

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
