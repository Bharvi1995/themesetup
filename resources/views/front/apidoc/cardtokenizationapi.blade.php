<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1.0, shrink-to-fit=no">
    <title>{{ config('app.name') }} | Merchant Card Tokenization API Document</title>
    <link rel="icon" type="image/png" sizes="16x16" href="{{ storage_asset('NewTheme/images/favicon.ico') }}">

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&family=Roboto:wght@100;300;400;500;700;900&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" type="text/css"
        href="{{ storage_asset('theme/API-assets/vendor/bootstrap/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" type="text/css"
        href="{{ storage_asset('theme/API-assets/vendor/font-awesome/css/all.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('theme/API-assets/css/stylesheet.css') }}" />

</head>

<body data-spy="scroll">
    <div class="preloader">
        <div class="lds-ellipsis">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>

    <div id="main-wrapper">
        <header id="header" class="sticky-top">
            <nav class="primary-menu navbar navbar-expand-lg navbar-dropdown-dark">
                <div class="container-fluid">
                    <button id="sidebarCollapse" class="navbar-toggler d-block d-md-none"
                        type="button"><span></span><span class="w-75"></span><span></span></button>
                    <a class="logo ml-md-3" href="{{ url('dashboard') }}">
                        <img src="{{ storage_asset('NewTheme/images/Logo.png') }}" width="150" alt="">
                    </a>
                    <button class="navbar-toggler ml-auto" type="button" data-toggle="collapse"
                        data-target="#header-nav"><span></span><span></span><span></span></button>

                    <div id="header-nav" class="collapse navbar-collapse justify-content-end">
                        <ul class="navbar-nav">
                            <li>
                                @if(Auth::check())
                                <a target="_blank" href="{{ url('dashboard') }}">Dashboard</a>
                                @else
                                <a target="_blank" href="{{ url('login') }}">Sign In</a>
                                @endif
                            </li>

                            <li>
                                <a href="{{ route('api-document') }}">Back</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>

        <div id="content" role="main">
            <div class="idocs-navigation bg-light">
                <ul class="nav flex-column ">
                    <li class="nav-item">
                        <a class="nav-link active" href="#APIRequest" id="apiRequest">Card Tokenization API</a>
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link" href="#CreateCardToken">Create Card Token</a>
                                <ul class="nav flex-column">
                                    <li class="nav-item">
                                        <a class="nav-link" href="#CreateCardToken">Request</a>
                                        <a class="nav-link" href="#CreateCardTokenResponse">Response</a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#CreateTransaction">Create Transaction</a>
                                <ul class="nav flex-column">
                                    <li class="nav-item">
                                        <a class="nav-link" href="#CreateTransaction">Request</a>
                                        <a class="nav-link" href="#APIResponse">Response</a>
                                        <a class="nav-link" href="#ValidationErrors">Validation Errors</a>
                                        <a class="nav-link" href="#SuccessfulResponse">Successful Response</a>
                                        <a class="nav-link" href="#DeclinedResponse">Declined Response</a>
                                        <a class="nav-link" href="#ThreeDResponse">3Ds Response</a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item"><a class="nav-link" href="#Webhooks">Webhooks</a></li>
                        </ul>
                    </li>
                </ul>
            </div>

            <div class="idocs-content">
                <div class="container">
                    <section id="CreateCardToken">
                        <div class="row">
                            <div class="col-md-7 idocs-content-left">
                                <h2>Card Tokenization API Documentation</h2>
                                <h4>How to create card token</h4>
                                <p class="lead">For, generate card token you just need the pass following values in your
                                    api call.</p>

                                <h4>Request Parameter *</h4>
                                <table class="table table-bordered table-striped">
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
                                    </tbody>
                                </table>
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong> Non-3DS Testing card data - </strong>
                                        <pre>card_no : 4242 4242 4242 4242
ccExpiryMonth : 02
ccExpiryYear : 2026
cvvNumber : 123</pre>
                                    </div>
                                    <div class="col-md-6">
                                        <strong> 3DS Testing card data - </strong>
                                        <pre>card_no : 4000 0027 6000 3184
ccExpiryMonth : 02
ccExpiryYear : 2026
cvvNumber : 123</pre>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5 idocs-content-right">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <strong>Live URL - </strong> <br><span class="text-primary">{{ env('APP_URL')
                                            }}/api/card-tokenization</span>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <strong>Method - </strong> <span class="badge badge-primary">POST</span>
                                    </div>
                                    <div class="col-md-12">
                                        <strong>API Call Example</strong>
                                        <pre>// You can call our API following curl post example
$url = "{{ env('APP_URL') }}/api/card-tokenization";
$key = "Your API Key";
// Fill with real customer info
$data = [
    'card_no' => '4242424242424242',
    'ccExpiryMonth' => '02',
    'ccExpiryYear' => '2026',
    'cvvNumber' => '123',
];

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_HTTPHEADER,[
    'Content-Type: application/json'
]);
$response = curl_exec($curl);
curl_close($curl);

$responseData = json_decode($response);
    
print_r($responseData);                    
}</pre>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </section>

                    <section id="CreateCardTokenResponse">
                        <div class="row">
                            <div class="col-md-7 idocs-content-left">
                                <h4>Validation Errors</h4>
                                <p class="lead">If in case of validation errors in request, response will be like:</p>
                            </div>
                            <div class="col-md-5 idocs-content-right">
                                <pre>{
    "status": "fail",
    "message": "Some parameters are missing or invalid request data, please check 'errors' parameter for more details.",
    "errors": {
        "ccExpiryYear": [
            "The cc expiry year must be at least 4 characters."
        ]
    }
}</pre>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-7 idocs-content-left">
                                <h4>Success Response</h4>
                                <p class="lead">Success response will be like:</p>
                            </div>
                            <div class="col-md-5 idocs-content-right">
                                <pre>{
    "status": "success",
    "card_token": "Y0R0WHQvT0thRVFGUGlOdWNsNmxVb3ZvNjYwYWJUb3cxcGtBZkQ3Mk52ND0="
}</pre>
                            </div>
                        </div>
                    </section>

                    <section id="CreateTransaction">
                        <div class="row">
                            <div class="col-md-7 idocs-content-left">
                                <h4>How to create transaction using card token</h4>
                                <p class="lead">You need to pass the following values in transaction create api using
                                    carad token.</p>

                                <h4>Request Parameter *</h4>
                                <table class="table table-bordered table-striped">
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
                                            <td>card_token</td>
                                            <td>Yes</td>
                                            <td>String</td>
                                            <td>pass here generated card token.</td>
                                        </tr>
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
                            <div class="col-md-5 idocs-content-right">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <strong>Live URL - </strong> <br><span class="text-primary">{{ env('APP_URL')
                                            }}/api/card-tokenization-transaction</span>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <strong>Method - </strong> <span class="badge badge-primary">POST</span>
                                    </div>
                                    <div class="col-md-12">
                                        <strong>API Call Example</strong>
                                        <pre>// You can call our API following curl post example
$url = "{{ env('APP_URL') }}/api/card-tokenization-transaction";
$key = "Your API Key";
$card_token = 'generated card_token'
// Fill with real customer info
$data = [
    'card_token' => $card_token,
    'api_key' => $key,
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
    'response_url' => 'https://yourdomain.com/callback.php',
    'webhook_url' => 'https://yourdomain.com/notification.php',
];

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_HTTPHEADER,[
    'Content-Type: application/json'
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
}</pre>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </section>

                    <section id="APIResponse">
                        <div class="row">
                            <div class="col-md-7 idocs-content-left">
                                <h4>Response</h4>
                                <p class="lead">After a successful CURL request, the response will be sent in JSON
                                    format.</p>

                                <h6>Mainly there are 3 types of response with the following Parameter:</h6>
                                <pre>1.) “status”:"fail” :  Transaction is declined.
2.) “status”:"success” : Transaction is success.
3.) “status”:"3d_redirect” : 3D secure redirection is required to complete the transaction</pre>

                                <h6>Success, Declined or 3ds response.</h6>
                                <p>If response contains “status”:"fail” or “status”:"success” it means transaction is
                                    complete and it doesn’t need to redirect to 3DS URL.</p>
                            </div>
                            <div class="col-md-5 idocs-content-right"></div>
                        </div>
                    </section>

                    <section id="ValidationErrors">
                        <div class="row">
                            <div class="col-md-7 idocs-content-left">
                                <h4>Validation Errors</h4>
                                <p class="lead">If in case of validation errors in request, response will be like:</p>
                            </div>
                            <div class="col-md-5 idocs-content-right">
                                <pre>{
    "status": "fail",
    "message": "Some parameters are missing or invalid request data, please check 'errors' parameter for more details.",
    "errors": {
        "phone_no": [
            "The phone no field is required."
        ]
    },
    "data": {
        "order_id": null,
        "amount": "20",
        "currency": "USD",
        "email": "example@mail.com",
        "customer_order_id": "GH56HJ86285CVP"
    }
}</pre>
                            </div>
                        </div>
                    </section>

                    <section id="SuccessfulResponse">
                        <div class="row">
                            <div class="col-md-7 idocs-content-left">
                                <h4>Successful Response</h4>
                                <p class="lead">This is successful transaction response</p>
                            </div>
                            <div class="col-md-5 idocs-content-right">
                                <pre>{
    "status": "success",
    "message": "Your transaction has been processed successfully.",
    "data": {
        "order_id": "16249643005FIFA4ARBU",
        "amount": "20",
        "currency": "USD",
        "email": "example@mail.com",
        "customer_order_id": "GH56HJ86285CVP"
    }
}</pre>
                            </div>
                        </div>
                    </section>

                    <section id="DeclinedResponse">
                        <div class="row">
                            <div class="col-md-7 idocs-content-left">
                                <h4>Declined Response</h4>
                                <p class="lead">This is the declined transaction response</p>

                            </div>
                            <div class="col-md-5 idocs-content-right">
                                <pre>{
    "status": "fail",
    "message": "Your card number is incorrect.",
    "data": {
        "order_id": "16249643005FIFA4ARBU",
        "amount": "20",
        "currency": "USD",
        "email": "example@mail.com",
        "customer_order_id": "GH56HJ86285CVP"
    }
}</pre>
                            </div>
                        </div>
                    </section>

                    <section id="ThreeDResponse">
                        <div class="row">
                            <div class="col-md-7 idocs-content-left">
                                <h4>3Ds secure Json Response type</h4>
                                <p class="lead">If the response returns “status”:"3d_redirect” need to redirect the
                                    “redirect_3ds_url”</p>

                                <h6>
                                    Response After 3DS
                                </h6>
                                <p>After 3D secure is completed, the user will be redirected to merchant website.</p>

                                <p>If transaction will be successful, the user will redirect to ”response_url” with
                                    response in query string similar to the one below:</p>
                                <pre>https://response_url?status=success&message=Your%20transaction%20was%20success&order_id=16249643005FIFA4ARBU&customer_order_id=GH56HJ86285CVP</pre>

                                <p>If the transaction fails, the user will be redirected to “response_url” with response
                                    in query string as follows:</p>

                                <pre>https://response_url?status=fail&message=Your%20card%20number%20is%20incorrect.",&order_id=16249643005FIFA4ARBU&customer_order_id=GH56HJ86285CVP</pre>
                            </div>
                            <div class="col-md-5 idocs-content-right">
                                <pre>{
    "status": "3d_redirect",
    "message": "3DS link generated successfully, please redirect to 'redirect_3ds_url'.",
    "redirect_3ds_url": {{ env('APP_URL') }}"/payment/test-transaction/DMZB1624964217",
    "customer_order_id": "GH56HJ86285CVP",
    "api_key": "your_api_key"
}</pre>
                            </div>
                        </div>
                    </section>

                    <section id="Webhooks">
                        <div class="row">
                            <div class="col-md-7 idocs-content-left">
                                <h4>Webhooks</h4>
                                <p class="lead">Webhooks events are transaction callbacks that sends notifications of
                                    transaction to the merchant server. If you want to receive webhooks, then send
                                    "webhook_url" parameter with initial request(See above request example).</p>

                                <p>Here are the simple explanation of each parameter:</p>

                                <pre>1.) order_id : Transaction reference number of our system.
2.) customer_order_id: Merchant transaction reference.
3.) transaction_status: "success" / "fail" / "pending" / "blocked".
4.) reason: Response from the bank about transaction status.
5.) currency: Currency of the transaction.
6.) amount: Amount of the transaction.
7.) email: Email of the transaction.
8.) transaction_date: Date of the transaction.</pre>
                            </div>
                            <div class="col-md-5 idocs-content-right">
                                <strong> Webhook Example</strong>

                                <p>Here are the example of webhook notification request:</p>
                                <pre>{
    "order_id": "16249643005FIFA4ARBU",
    "customer_order_id": "GH56HJ86285CVP",
    "transaction_status": "success",
    "reason": "Your transaction has been processed successfully.",
    "currency": "USD",
    "amount": "20",
    "transaction_date": "2021-06-23 04:38:51"
}</pre>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

        </div>

        <footer id="footer">
            <div class="container">
                Copyright &copy; Designed &amp; Developed by <a href="{{ config('app.url') }}" target="_blank">{{
                    config('app.name') }}</a> {{ date('Y') }}
            </div>
        </footer>
    </div>
    <!-- Document Wrapper end -->

    <!-- Back To Top -->
    <a id="back-to-top" data-toggle="tooltip" title="Back to Top" href="javascript:void(0)">
        <i class="fa fa-chevron-up"></i>
    </a>

    <!-- JavaScript
============================ -->
    <script src="{{ storage_asset('themeAdmin/js/jquery-latest.min.js') }}"></script>
    <script src="{{ storage_asset('theme/API-assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ storage_asset('theme/API-assets/vendor/jquery.easing/jquery.easing.min.js') }}"></script>
    <script src="{{ storage_asset('theme/API-assets/js/theme.js') }}"></script>
</body>

</html>