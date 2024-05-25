<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1.0, shrink-to-fit=no">
    <title>{{ config('app.name') }} | Merchant Bank Pay API Document</title>
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
                        <a class="nav-link active" href="#APIRequestCrypto">Bank Payment API</a>
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link" href="#APIRequestCrypto">Request</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#APIResponseCrypto">Response</a>
                                <ul class="nav flex-column">
                                    <li class="nav-item">
                                        <a class="nav-link" href="#ValidationErrorsCrypto">Validation Errors</a>
                                        <a class="nav-link" href="#SuccessfulResponseCrypto">Successful Response</a>
                                        <a class="nav-link" href="#ThreeDResponseCrypto">From {{env('APP_NAME')}}</a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item"><a class="nav-link" href="#WebhooksCrypto">Webhooks</a></li>
                        </ul>
                    </li>
                </ul>
            </div>

            <div class="idocs-content">
                <div class="container">
                    <section id="APIRequestCrypto">
                        <div class="row">
                            <div class="col-md-7 idocs-content-left">
                                <h2>Bank API Documentation</h2>
                                <h4>How to integrate Bank API</h4>
                                <p class="lead">This is Bank Page API documentation. In this API, User details will be
                                    sent to {{env('APP_NAME')}} Server with curl request, where as card details will be
                                    loaded to {{env('APP_NAME')}} server. Follow the below steps to integrate hosted
                                    page API with {{env('APP_NAME')}}.</p>

                                <h4>Using curl request</h4>
                                <p>The following parameters should be sent to the hosted page.</p>
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
                            <div class="col-md-5 idocs-content-right">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <strong>Testing URL - </strong> <br><span class="text-primary">{{ env('APP_URL')
                                            }}/api/test/bank/transaction</span><br />
                                        <strong>Live URL - </strong> <br><span class="text-primary">{{ env('APP_URL')
                                            }}/api/bank/transaction</span>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <strong>Method - </strong> <span class="badge badge-primary">POST</span>
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

                            </div>
                        </div>
                    </section>

                    <section id="APIResponseCrypto">
                        <div class="row">
                            <div class="col-md-7 idocs-content-left">
                                <h4>Response</h4>
                                <p class="lead">After you redirect to url payment_redirect_url, a form will be loaded
                                    over {{env('APP_NAME')}} payment page. You will need to fill credit card details if
                                    asked</p>
                            </div>
                            <div class="col-md-5 idocs-content-right"></div>
                        </div>
                    </section>

                    <section id="ValidationErrorsCrypto">
                        <div class="row">
                            <div class="col-md-7 idocs-content-left">
                                <h4>Validation Errors</h4>
                                <p class="lead">If in case there will be validation errors in the request, the response
                                    will be similar to the following:</p>
                            </div>
                            <div class="col-md-5 idocs-content-right">
                                <pre>{
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
}</pre>
                            </div>
                        </div>
                    </section>

                    <section id="SuccessfulResponseCrypto">
                        <div class="row">
                            <div class="col-md-7 idocs-content-left">
                                <h4>Successful Response</h4>
                                <p class="lead">After a successful request, the response will be returned in JSON
                                    format.</p>
                            </div>
                            <div class="col-md-5 idocs-content-right">
                                <strong>Example</strong>
                                <pre>{
    "status": "3d_redirect",
    "message": "3DS link generated successfully, please redirect to 'redirect_3ds_url'.",
    "redirect_3ds_url": "{{ env('APP_URL') }}/hosted-pay/input-card/45869JIOL458",
    "customer_order_id": null,
    "api_key": "your_api_key"
}</pre>
                                <p>If the request data contains a valid format, then the above response will be
                                    returned. You will need to redirect to “payment_redirect_url” for payment page
                                    before “valid_till”.</p>
                            </div>
                        </div>
                    </section>

                    <section id="ThreeDResponseCrypto">
                        <div class="row">
                            <div class="col-md-7 idocs-content-left">
                                <h4>Response from {{env('APP_NAME')}}</h4>
                                <p class="lead">
                                    After Credit card form is completely filled up, the user must press the Pay button.
                                    This request will take some time, if user card has 3D secure feature enabled, it
                                    will also redirect the process to a 3D secure page, where user will be asked to
                                    input PIN or OTP if asked. After the entire process is complete, user will be
                                    redirected to the merchant website and will reflect the transaction status.
                                </p>
                            </div>
                            <div class="col-md-5 idocs-content-right">
                                If transaction is successful, the user will be redirected to ”response_url” with the
                                response in query string like the one below:
                                <br>
                                <pre
                                    class="pre"><a href="{{ env('APP_URL') }}/success?status=success&message=Your%20transaction%20was%20success&order_id=20190000458521&customer_order_id=456789521365">{{ env('APP_URL') }}/success?status=success&message=Your%20transaction%20was%20success&order_id=20190000458521&customer_order_id=456789521365</a></pre>
                                If the transaction fails, the user will redirect as well to “response_url” with the
                                response query string similar to the one below:
                                <pre
                                    class="pre"><a href="{{ env('APP_URL') }}/fail?status=fail&message=Activity%20limit%20exceeded.&order_id=20190000458521&customer_order_id=456789521365">{{ env('APP_URL') }}/fail?status=fail&message=Activity%20limit%20exceeded.&order_id=20190000458521&customer_order_id=456789521365</a></pre>
                            </div>
                        </div>
                    </section>

                    <section id="WebhooksCrypto">
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