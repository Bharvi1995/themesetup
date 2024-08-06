<?php

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

Route::group(['middleware' => ['adminauth', 'query_response_time']], function () {

    //Log View // optional filename passed in query string
    // Route::get('logs', 'CommonController@readLogs')->name('logs-read');

    //Aws S3 Bucket Check
    // Route::get('check/s3bucket', 'CommonController@checkS3Bucket')->name('check-s3bucket');

    // Test send-bulk-mail
    // Route::get('send-bulk-mail', 'SendBulkMailController@sendBulkMail')->name('send-bulk-mail');

    // Test API
    // Route::get('payoutmonnetpayments', 'TestController@payoutmonnetpayments')->name('payoutmonnetpayments');

    // Route::get('nowpayment/qrcode', function () {
    //     return view('gateway.nowpayment.qrcode');
    // })->name('nowpayment/qrcode');

    // Route::get('iframe-test', function () {
    //     return view('iframe');
    // })->name('iframe-test');

    // Route::get('cardDetails-test', function () {
    //     return view('cardDetails');
    // })->name('cardDetails-test');

    // // test db connection
    // Route::get('check-db-connection', function () {
    //     // dd(convertUSD(2840.00, 110.00));
    //     if (DB::connection()->getDatabaseName()) {
    //         echo "conncted sucessfully to database " . DB::connection()->getDatabaseName();
    //     }
    // });

    // test mail
    // Route::get('/sendtestmail', function (Request $request) {
    //     $toemail = $request->mail;
    //     $data['title'] = "This is Test Mail Tuts Make";
    //     Mail::send('emails.test', $data, function ($message) use ($toemail) {

    //         $TEST_EMAIL_TO = $toemail;
    //         $TEST_EMAIL_TO_NAME = env("TEST_EMAIL_TO_NAME");
    //         $message->to($TEST_EMAIL_TO, $TEST_EMAIL_TO_NAME)->subject('this is test bulk Mail');
    //     });
    //     // for ($i=0; $i < 5; $i++) {
    //     // }
    //     echo "Done";
    // });
});


Route::get('/', function () {
    return view('auth.login');
})->name('login');

// Auth Route
Auth::routes();

/*********************User Register Module Start *************************************/
Route::get('registrationform', 'ApplyNowController@index')->name('registrationform');
Route::post('apply-now', 'ApplyNowController@store')->name('applynow-store');
Route::get('user/activation/{id}', 'ApplyNowController@verifyUserEmail')->name('user-activate');
Route::get('user/confirmation', 'ApplyNowController@confirmMailActive')->name('user/confirmation');
Route::get('user-email-activate', 'ApplyNowController@verifyUserChangeEmail')->name('user-email-activate');

// Route::get('testpay-otp', 'Auth\LoginController@otpform')->name('testpay-otp');
// Route::get('resend-otp', 'Auth\LoginController@resendotp')->name('resend-otp');
// Route::post('testpay-otp-store', 'Auth\LoginController@checkotp')->name('testpay-otp-store');
Route::post('testpay-mobile-no-store', 'Auth\LoginController@addMobileNo')->name('testpay-mobile-no-store');

/*********************User Register Module End *************************************/

/*********************Admin Routes Module End *************************************/
Route::get('paylaksa/login', 'Auth\AdminAuthController@getLogin')->name('paylaksa/login');
Route::post('paylaksa/login', 'Auth\AdminAuthController@postLogin')->name('paylaksa/login');
// Route::get('paylaksa/testpay-otp', 'Auth\AdminAuthController@otpform')->name('admin.testpay-otp');
// Route::get('paylaksa/resend-otp', 'Auth\AdminAuthController@resendotp')->name('admin.resend-otp');
// Route::post('paylaksa/testpay-otp-store', 'Auth\AdminAuthController@checkotp')->name('admin.testpay-otp-store');
Route::post('paylaksa/testpay-mobile-no-store', 'Auth\AdminAuthController@addMobileNo')->name('admin.testpay-mobile-no-store');
Route::get('paylaksa/logout', 'Auth\AdminAuthController@logout')->name('paylaksa/logout');

Route::get('paylaksa/password/reset', 'Auth\AdminAuthController@adminForgetPassword')->name('admin-password-reset');
Route::post('paylaksa/password/email', 'Auth\AdminAuthController@adminForgetEmail')->name('admin-password-email');
Route::get('paylaksa/password/reset/{id}', 'Auth\AdminAuthController@adminForgetPasswordForm')->name('admin-password-reset-form');
Route::post('paylaksa/password/resetForm', 'Auth\AdminAuthController@adminForgetPasswordFormPost')->name('admin-password-resetForm');
Route::get('admin-email-activate', 'AdminController@verifyAdminChangeEmail')->name('admin-email-activate');
// Merchant dashboard login from admin side.
Route::get('/userLogin', 'AdminController@userLoginByAdmin')->name('userLogin');
Route::get('/subUserLogin', 'AdminController@subUserLoginByAdmin')->name('subUserLogin');
// Agent dashboard login from admin side.
// Route::get('/agentLogin', 'AdminController@agentLoginByAdmin')->name('agentLogin');
// Route::get('/bankLogin', 'AdminController@bankLoginByAdmin')->name('bankLogin');
// Route::get('/wlAgentLogin', 'AdminController@wlAgentLoginByAdmin')->name('wlAgentLogin');
/*********************Admin Routes Module End *************************************/


/*
|--------------------------------------------------------------------------
| Bank User Routes
|--------------------------------------------------------------------------
*/
// Route::get('bank/login', 'Auth\BankUserAuthController@getLogin')->name('bank/login');
// Route::post('bank/login', 'Auth\BankUserAuthController@postLogin')->name('bank/login');
// Route::get('bank/logout', 'Auth\BankUserAuthController@logout')->name('bank/logout');
// Route::get('bank/register', 'Auth\BankUserAuthController@register')->name('bank.register');
// Route::post('bank/store', 'Auth\BankUserAuthController@store')->name('bank.store');
// Route::get('bank-activate/{id}', 'Auth\BankUserAuthController@verifyUserEmail')->name('bank-activate');

// Route::get('bank/testpay-otp', 'Auth\BankUserAuthController@otpform')->name('bank.testpay-otp');
// Route::get('bank/resend-otp', 'Auth\BankUserAuthController@resendotp')->name('bank.resend-otp');
// Route::post('bank/testpay-otp-store', 'Auth\BankUserAuthController@checkotp')->name('bank.testpay-otp-store');

// Route::get('bank/password/reset', 'Auth\BankUserAuthController@agentForgetPassword')->name('bank-password-reset');
// Route::post('bank/password/email', 'Auth\BankUserAuthController@bankForgetEmail')->name('bank-password-email');
// Route::get('bank/password/reset/{id}', 'Auth\BankUserAuthController@bankForgetPasswordForm')->name('bank-password-reset-form');
// Route::post('bank/password/resetForm', 'Auth\BankUserAuthController@bankForgetPasswordFormPost')->name('bank-password-resetForm');
/*
|--------------------------------------------------------------------------
| Agent User Routes
|--------------------------------------------------------------------------
*/
Route::get('affiliate/login', 'Auth\AgentUserAuthController@getLogin')->name('rp/login');
Route::post('affiliate/login', 'Auth\AgentUserAuthController@postLogin')->name('rp/login');
Route::get('affiliate/logout', 'Auth\AgentUserAuthController@logout')->name('rp/logout');
Route::get('affiliate/register', 'Auth\AgentUserAuthController@register')->name('agent.register');
Route::post('affiliate/store', 'Auth\AgentUserAuthController@store')->name('agent.store');
Route::get('affiliate/activate/{token}', 'Auth\AgentUserAuthController@verifyUserEmail')->name('agent-activate');

Route::get('affiliate/password/reset', 'Auth\AgentUserAuthController@agentForgetPassword')->name('rp-password-reset');
Route::post('affiliate/password/email', 'Auth\AgentUserAuthController@agentForgetEmail')->name('rp-password-email');
Route::get('affiliate/password/reset/{id}', 'Auth\AgentUserAuthController@agentForgetPasswordForm')->name('rp-password-reset-form');
Route::post('affiliate/password/resetForm', 'Auth\AgentUserAuthController@agentForgetPasswordFormPost')->name('rp-password-resetForm');

// Route::get('rp/testpay-otp', 'Auth\AgentUserAuthController@otpform')->name('rp.testpay-otp');
// Route::get('rp/resend-otp', 'Auth\AgentUserAuthController@resendotp')->name('rp.resend-otp');
// Route::post('rp/testpay-otp-store', 'Auth\AgentUserAuthController@checkotp')->name('rp.testpay-otp-store');

/*
|--------------------------------------------------------------------------
| WL Agent User Routes
|--------------------------------------------------------------------------
*/
// Route::get('wl/rp/login', 'Auth\WLAgentUserAuthController@getLogin')->name('wl/rp/login');
// Route::post('wl/rp/login', 'Auth\WLAgentUserAuthController@postLogin')->name('wl/rp/login');
// Route::get('wl/rp/logout', 'Auth\WLAgentUserAuthController@logout')->name('wl/rp/logout');

// Route::get('wl/rp/password/reset', 'Auth\WLAgentUserAuthController@agentForgetPassword')->name('wl-rp-password-reset');
// Route::post('wl/rp/password/email', 'Auth\WLAgentUserAuthController@agentForgetEmail')->name('wl-rp-password-email');
// Route::get('wl/rp/password/reset/{id}', 'Auth\WLAgentUserAuthController@agentForgetPasswordForm')->name('wl-rp-password-reset-form');
// Route::post('wl/rp/password/resetForm', 'Auth\WLAgentUserAuthController@agentForgetPasswordFormPost')->name('wl-rp-password-resetForm');

// Route::get('wl/rp/testpay-otp', 'Auth\WLAgentUserAuthController@otpform')->name('wl.rp.testpay-otp');
// Route::get('wl/rp/resend-otp', 'Auth\WLAgentUserAuthController@resendotp')->name('wl.rp.resend-otp');
// Route::post('wl/rp/testpay-otp-store', 'Auth\WLAgentUserAuthController@checkotp')->name('wl.rp.testpay-otp-store');


Route::get('/transaction-documents-upload', 'TransactionDocumentsUploadController@transactionDocumentsUpload')->name('transaction-documents-upload');
Route::post('/transaction-documents-upload', 'TransactionDocumentsUploadController@store')->name('transaction-documents-upload');
Route::post('/transaction-documents-upload-edit', 'TransactionDocumentsUploadController@update')->name('transaction-documents-upload-edit');
Route::get('downloadDocumentsUploade', 'TransactionsController@downloadDocumentsUploade')->name('user.downloadDocumentsUploade');

// Route::get('/service_agreement/upload', 'AgreementDocumentsUploadController@index')->name('agreement-documents-upload');
// Route::post('/service_agreement/upload', 'AgreementDocumentsUploadController@store')->name('agreement-documents-upload');
Route::get('/rpservice_agreement/upload', 'AgreementDocumentsUploadController@uploadRP')->name('rp-agreement-documents-upload');
Route::post('/rpservice_agreement/upload', 'AgreementDocumentsUploadController@storeRP')->name('rp-agreement-documents-upload');

/********************* Hosted API route Start *************************************/
Route::get('hosted-pay/select-payment-method/{session}', 'API\HostedAPIController@paymentTypeSelect')->name('hostedAPI.paymentTypeSelect');
Route::post('hosted-pay/submit-payment-method/{session}', 'API\HostedAPIController@paymentTypeSubmit')->name('hostedAPI.paymentTypeSubmit');
Route::get('hosted/card/{session}', 'API\HostedAPIController@cardForm')->name('hostedAPI.cardForm');
Route::post('hosted-pay/input-card/{session}', 'API\HostedAPIController@cardSubmit')->name('hostedAPI.cardSubmit');
Route::get('hosted-pay/cancel-transaction/{session}', 'API\HostedAPIController@cancelTransaction')->name('hostedAPI.cancelTransaction');
/********************* Hosted API route End *************************************/

/********************* iframe route Start *************************************/
Route::get('checkout/input-details/{token}', 'API\IframeController@index')->name('iframe.index');
Route::post('checkout/submit-details/{token}', 'API\IframeController@submit')->name('iframe.submit');
/********************* iframe route Start *************************************/

/********************* Gateway route Start *************************************/
// test gateway
Route::get('payment/testgateway/{session_id}', 'Repo\PaymentGateway\TestGateway@stripeForm')->name('test-stripe');
Route::post('payment/submittestgateway/{session_id}', 'Repo\PaymentGateway\TestGateway@test3DSFormSubmit')->name('test-stripe-submit');

// stripe live gateway
Route::get('payment/stripe/return/{session_id}', 'Repo\PaymentGateway\Stripe@return')->name('stripe.return');

//Dixonpay gateway
Route::any('dixonpayvisa/return/{id}', 'Repo\PaymentGateway\Dixonpayvisa@return')->name("dixonpayvisa.return");
Route::get('dixonpayvisa/form/{id}', 'Repo\PaymentGateway\Dixonpayvisa@form')->name("dixonpayvisa.form");
Route::post("dixonpayvisa/notify/{id}", 'Repo\PaymentGateway\Dixonpayvisa@notify')->name("dixonpayvisa.notify");

// opay gateway
Route::get('opay/input/{type}/{session}/{order}', 'Repo\PaymentGateway\OpayGateway@inputForm')->name('opay.inputForm');
Route::post('opay/input-submit/{type}/{session}/{order}', 'Repo\PaymentGateway\OpayGateway@inputResponse')->name('opay.inputResponse');
Route::get('opay/waiting/{session}/{order}/{loop_no}', 'Repo\PaymentGateway\OpayGateway@pendingBlade')->name('opay.pendingBlade');
Route::post('opay/waiting-submit/{session}/{order}/{loop_no}', 'Repo\PaymentGateway\OpayGateway@pendingBladeSubmit')->name('opay.pendingBladeSubmit');
Route::get('opay/redirect/{session}', 'Repo\PaymentGateway\OpayGateway@redirect')->name('opay.redirect');
Route::post('opay/callback', 'Repo\PaymentGateway\OpayGateway@notify')->name('opay.notify');
Route::get('opay/cronjob/run', 'Repo\PaymentGateway\OpayGateway@cronjob')->name('opay.cronjob');
Route::get('opay/get-status', 'Repo\PaymentGateway\OpayGateway@getOpayStatus')->name('opay.get-status');

// NowPayment gateway
Route::post('nowpayments-callback/{session}', 'Repo\PaymentGateway\NowPaymentsCard@callback')->name('nowpayments-callback');
Route::post('nowpayments-success-callback/{session}', 'Repo\PaymentGateway\NowPaymentsCard@successCallback')->name('nowpayments-success-callback');
Route::post('nowpayments-cancel-callback/{session}', 'Repo\PaymentGateway\NowPaymentsCard@cancelCallback')->name('nowpayments-cancel-callback');
Route::post('nowpayments-crypto-callback/{session}', 'Repo\PaymentGateway\NowPayments@callback')->name('nowpayments-crypto-callback');
Route::post('nowpayments-cryptosuccess-callback/{session}', 'Repo\PaymentGateway\NowPayments@successCallback')->name('nowpayments-cryptosuccess-callback');
Route::post('nowpayments-cryptocancel-callback/{session}', 'Repo\PaymentGateway\NowPayments@cancelCallback')->name('nowpayments-cryptocancel-callback');

//OnlineNaira gateway
Route::get('onlinenaira/input/{session}', 'Repo\PaymentGateway\OnlineNaira@inputForm')->name('onlinenaira.inputForm');
Route::get('onlinenaira-return', 'Repo\PaymentGateway\OnlineNaira@return')->name('onlinenaira.return');
Route::post('onlinenaira-notify', 'Repo\PaymentGateway\OnlineNaira@notify')->name('onlinenaira.notify');

Route::get('onlinenaira-cancel/{session}', 'Repo\PaymentGateway\OnlineNaira@cancel')->name('onlinenaira.cancel');
Route::get('onlinenaira-callback/{session}', 'Repo\PaymentGateway\OnlineNaira@callback')->name('onlinenaira.callback');
Route::post('onlinenaira-callbacknotify/{session}', 'Repo\PaymentGateway\OnlineNaira@callbacknotify')->name('onlinenaira.callbacknotify');
Route::post('onlinenaira/input-submit/{session}', 'Repo\PaymentGateway\OnlineNaira@inputResponse')->name('onlinenaira.inputResponse');

// TripleA Gateway Route
Route::get('triplea-success-url', 'Repo\PaymentGateway\TrippleA@tripleaSuccessUrl')->name('triplea-success-url');
Route::get('triplea-cancel-url', 'Repo\PaymentGateway\TrippleA@tripleaCancelUrl')->name('triplea-cancel-url');
Route::post('triplea-webhook-url', 'Repo\PaymentGateway\TrippleA@tripleaWebhook')->name('triplea-webhook-url');

// wyre
Route::get('wyre/redirect/{session}', 'Repo\PaymentGateway\Wyre@redirect')->name('Wyre.redirect');
Route::get('wyre/form', 'Repo\PaymentGateway\Wyre@form')->name('Wyre.form');
Route::post('wyre/form-submit', 'Repo\PaymentGateway\Wyre@submit')->name('Wyre.submit');
Route::get('wyre/cancel-submit/{session}', 'Repo\PaymentGateway\Wyre@cancel')->name('Wyre.cancel');
Route::post('wyre/callback/notify', 'Repo\PaymentGateway\Wyre@notify')->name('Wyre.notify');



//Alps Route
Route::get('alps/success/{session}', 'Repo\PaymentGateway\Alps@succesRedirect')->name('alps.success');
Route::get('alps/fail/{session}', 'Repo\PaymentGateway\Alps@failRedirect')->name('alps.fail');

//Alps Bank Route
Route::get('alpsbank/success/{session}', 'Repo\PaymentGateway\AlpsBank@succesRedirect')->name('alpsbank.success');
Route::get('alpsbank/fail/{session}', 'Repo\PaymentGateway\AlpsBank@failRedirect')->name('alpsbank.fail');

//Bitbaypay route
Route::post('bitbaypay/notify/{session}', 'Repo\PaymentGateway\Bitbaypay@notify')->name('bitbaypay.notify');
Route::get('bitbaypay/success/{session}', 'Repo\PaymentGateway\Bitbaypay@success')->name('bitbaypay.success');
Route::get('bitbaypay/failure/{session}', 'Repo\PaymentGateway\Bitbaypay@failure')->name('bitbaypay.failure');

// texcent gateway
Route::get('secure-gateway/redirect/{id}', 'Repo\PaymentGateway\Texcent@redirect')->name('texCent.redirect');
Route::post('secure-gateway/notify/{id}', 'Repo\PaymentGateway\Texcent@notify')->name('texCent.notify');

// Test crypto transaction
Route::get('payment/test-crypto-transaction/{session_id}', 'Repo\PaymentGateway\TestCrypto@testCryptoForm')->name('test-crypto-transaction');
Route::post('payment/test-crypto-transaction/{session_id}', 'Repo\PaymentGateway\TestCrypto@testCryptoFormSubmit')->name('test-crypto-transaction-submit');

// Test bank transaction
Route::get('payment/test-bank-transaction/{session_id}', 'Repo\PaymentGateway\TestBank@testBankForm')->name('test-bank-transaction');
Route::post('payment/test-bank-transaction/{session_id}', 'Repo\PaymentGateway\TestBank@testBankFormSubmit')->name('test-bank-transaction-submit');

// Test hosted api
Route::get('test/hosted/card/{session}', 'API\TestHostedAPIController@cardForm')->name('test.hostedAPI.cardForm');
Route::post('test-hosted/input-card/{session}', 'API\TestHostedAPIController@cardSubmit')->name('test.hostedAPI.cardSubmit');

// Cryptoxa
Route::post('cryptoxa/callback/{id}', 'Repo\PaymentGateway\Cryptoxa@callback')->name('cryptoxa-callback');
Route::get('cryptoxa/redirect/{id}', 'Repo\PaymentGateway\Cryptoxa@redirect')->name('cryptoxa-redirect');

// Interkassa
Route::any('interkassa/callback/{id}', 'Repo\PaymentGateway\Interkassa@callback')->name('interkassa-callback');
Route::any('interkassa/success/{id}', 'Repo\PaymentGateway\Interkassa@success')->name('interkassa-success');
Route::any('interkassa/fail/{id}', 'Repo\PaymentGateway\Interkassa@fail')->name('interkassa-fail');
Route::any('interkassa/pending/{id}', 'Repo\PaymentGateway\Interkassa@pending')->name('interkassa-pending');



// Rogerpay
Route::any('rogerpay/callback/{id}', 'Repo\PaymentGateway\Rogerpay@callback')->name('rogerpay-callback');
Route::any('rogerpay/notification/{id}', 'Repo\PaymentGateway\Rogerpay@notification')->name('rogerpay-notification');

// Paycos
Route::post('paycos/callback/{id}', 'Repo\PaymentGateway\Paycos@callback')->name('paycos-callback');
Route::get('paycos/success/{id}', 'Repo\PaymentGateway\Paycos@success')->name('paycos-success');
Route::get('paycos/fail/{id}', 'Repo\PaymentGateway\Paycos@fail')->name('paycos-fail');

// opennode
Route::post('opennode-callbackUrl/{sessiondata}', 'Repo\PaymentGateway\Opennode@callbackUrl')->name('opennode-callbackUrl');
Route::get('opennode-successUrl/{sessiondata}', 'Repo\PaymentGateway\Opennode@successUrl')->name('opennode-successUrl');

Route::any("vivawallet/redirect/{sessiondata}", 'Repo\PaymentGateway\Vivawallet@callBackUrl')->name("vivawallet.redirect");

// Trust Payment
Route::get('trust/confirmation/{id}', 'Repo\PaymentGateway\TrustPayment@confirmation')->name('trust-confirmation');
Route::get('trust/success/{id}', 'Repo\PaymentGateway\TrustPayment@success')->name('trust-success');
Route::get('trust/fail/{id}', 'Repo\PaymentGateway\TrustPayment@fail')->name('trust-fail');
Route::get('trust/decline/{id}', 'Repo\PaymentGateway\TrustPayment@decline')->name('trust-decline');
Route::post('trust/notification/{id}', 'Repo\PaymentGateway\TrustPayment@notification')->name('trust-notification');

// BTPay Payment
Route::get('btpay/confirmation/{id}/{card_detail}', 'Repo\PaymentGateway\BTPay@confirmation')->name('btpay-confirmation');
Route::post('btpay/confirmation', 'Repo\PaymentGateway\BTPay@confirmationFormSubmit')->name('btpay-confirmation-submit');

// Interkassa Upi Payment
Route::any('interkassa-upi/confirmation/{id}', 'Repo\PaymentGateway\InterkassaUpi@confirmation')->name('interkassa-upi-confirmation');
Route::post('interkassa-upi/confirmation', 'Repo\PaymentGateway\InterkassaUpi@confirmationFormSubmit')->name('interkassa-upi-confirmation-submit');
Route::any('interkassa-upi/success/{id}', 'Repo\PaymentGateway\InterkassaUpi@success')->name('interkassa-upi-success');
Route::any('interkassa-upi/fail/{id}', 'Repo\PaymentGateway\InterkassaUpi@fail')->name('interkassa-upi-fail');

// Interkassa NetBanking
Route::any('interkassa-net-banking/confirmation/{id}', 'Repo\PaymentGateway\InterkassaNetBanking@confirmation')->name('interkassa-net-banking-confirmation');
Route::post('interkassa-net-banking/confirmation', 'Repo\PaymentGateway\InterkassaNetBanking@confirmationFormSubmit')->name('interkassa-net-banking-confirmation-submit');
Route::any('interkassa-net-banking/success/{id}', 'Repo\PaymentGateway\InterkassaNetBanking@success')->name('interkassa-net-banking-success');
Route::any('interkassa-net-banking/fail/{id}', 'Repo\PaymentGateway\InterkassaNetBanking@fail')->name('interkassa-net-banking-fail');

// TrustSpay
Route::get('trustspay/confirmation/{id}/{card_detail}', 'Repo\PaymentGateway\TrustSpay@confirmation')->name('trustspay-confirmation');
Route::post('trustspay/confirmation', 'Repo\PaymentGateway\TrustSpay@confirmationFormSubmit')->name('trustspay-confirmation-submit');

// flutterwave direct
// Route::get('flutterwave-response', 'Repo\PaymentGateway\Flutterwave@responseFromFlutterwave')->name('flutterwave-response.responseFromFlutterwave');
Route::get('flutterwave-callback/{id}', 'Repo\PaymentGateway\Flutterwave@callback')->name('flutterwave-callback');

//VIPPass
Route::any('vippass/callback/{id}', 'Repo\PaymentGateway\VIPPass@callback')->name('vippass-callback');
Route::post('vippass/webhook/{id}', 'Repo\PaymentGateway\VIPPass@webhook')->name('vippass-webhook');

//QartPay
Route::get('qartpay/confirmation/{id}', 'Repo\PaymentGateway\QartPay@confirmation')->name('qartpay-confirmation');
Route::post('qartpay/callback/{id}', 'Repo\PaymentGateway\QartPay@callback')->name('qartpay-callback');

//Paythone
Route::get('paythrone/confirmation/{id}', 'Repo\PaymentGateway\Paythrone@confirmation')->name('paythrone-confirmation');
Route::post('paythrone/webhook', 'Repo\PaymentGateway\Paythrone@webhook')->name('paythrone-webhook');
Route::get('paythrone/redirect/{id}', 'Repo\PaymentGateway\Paythrone@redirect')->name('paythrone-redirect');

//AppsNmobile
Route::post('appsnmobile/callback/{id}', 'Repo\PaymentGateway\AppsNmobile@callback')->name('appsnmobile-callback');

// Chakra
Route::post('chakra/callback/{id}', 'Repo\PaymentGateway\Chakra@callback')->name('chakra-callback');
Route::get('chakra/return/{id}', 'Repo\PaymentGateway\Chakra@returnUrl')->name('chakra-returnUrl');
Route::get('chakra/success/{id}', 'Repo\PaymentGateway\Chakra@success')->name('chakra-success');
Route::get('chakra/failure/{id}', 'Repo\PaymentGateway\Chakra@failure')->name('chakra-failure');

//Gamepay Route
Route::get('gamepay/callback/{id}', 'Repo\PaymentGateway\Gamepay@callback')->name('gamepay.callback');

// FacilitaPay
Route::post('facilitapay/webhook', 'Repo\PaymentGateway\FacilitaPay@webhook')->name('facilitapay-webhook');

// Cellulant
Route::get('cellulant/confirmation/{id}', 'Repo\PaymentGateway\Cellulant@confirmation')->name('cellulant-confirmation');
Route::post('cellulant/success/{id}', 'Repo\PaymentGateway\Cellulant@success')->name('cellulant-success');
Route::post('cellulant/fail/{id}', 'Repo\PaymentGateway\Cellulant@fail')->name('cellulant-fail');
Route::get('cellulant/pending/{id}', 'Repo\PaymentGateway\Cellulant@pending')->name('cellulant-pending');
Route::post('cellulant/webhook/{id}', 'Repo\PaymentGateway\Cellulant@webhook')->name('cellulant-webhook');

// QikPay
Route::get('qikpay/confirmation/{id}', 'Repo\PaymentGateway\QikPay@confirmation')->name('qikpay-confirmation');
Route::post('qikpay/callback/{id}', 'Repo\PaymentGateway\QikPay@callback')->name('qikpay-callback');

Route::get('qikpay/form/{id}', 'Repo\PaymentGateway\QikPays2s@form')->name("qikpays2s.form");
Route::post('qikpay/submit/{id}', 'Repo\PaymentGateway\QikPays2s@formSubmit')->name("qikpays2s.submit");
Route::post('qikpay/formSendData/{id}', 'Repo\PaymentGateway\QikPays2s@formSendData')->name("qikpays2s.formSendData");
Route::post('qikpays2s/callback/{id}', 'Repo\PaymentGateway\QikPays2s@callback')->name('qikpays2s-callback');

Route::get('qikpay/upi/form/{id}', 'Repo\PaymentGateway\QikPayUPI@form')->name("qikpayupi.form");
Route::post('qikpay/upi/callback/{id}', 'Repo\PaymentGateway\QikPayUPI@callback')->name("qikpayupi.callback");

// AvalanchePay Route
Route::post('avalanchepay/api', 'Repo\PaymentGateway\AvalanchePay@api')->name('avalanchepay.api');
Route::get('avalanchepay/success/{id}', 'Repo\PaymentGateway\AvalanchePay@success')->name('avalanchepay.success');
Route::get('avalanchepay/cancel/{id}', 'Repo\PaymentGateway\AvalanchePay@cancel')->name('avalanchepay.cancel');

// DamaPay Route
Route::post('damapay/api', 'Repo\PaymentGateway\DamaPay@api')->name('damapay.api');
Route::get('damapay/success/{id}', 'Repo\PaymentGateway\DamaPay@success')->name('damapay.success');
Route::get('damapay/cancel/{id}', 'Repo\PaymentGateway\DamaPay@cancel')->name('damapay.cancel');

// Paybypago
Route::get('paybypago/callback/{id}', 'Repo\PaymentGateway\Paybypago@callback')->name('paybypago-callback');
Route::post('paybypago/notification/{id}', 'Repo\PaymentGateway\Paybypago@notification')->name('paybypago-notification');

Route::get('takepayment-confirmation/{id}', 'Repo\PaymentGateway\Takepayment@confirmation')->name('takepayment-confirmation');
Route::post('takepayment-callback/{id}', 'Repo\PaymentGateway\Takepayment@callback')->name('takepayment-callback');
Route::post('takepayment-redirect/{id}', 'Repo\PaymentGateway\Takepayment@redirect')->name('takepayment-redirect');

// Nihaopay
Route::get('nihaopay/confirmation/{id}', 'Repo\PaymentGateway\Nihaopay@confirmation')->name('nihaopay-confirmation');
Route::post('nihaopay/notification/{id}', 'Repo\PaymentGateway\Nihaopay@notification')->name('nihaopay-notification');
Route::get('nihaopay/callback/{id}', 'Repo\PaymentGateway\Nihaopay@callback')->name('nihaopay-callback');

// Wonderland
Route::get('wonderland/confirmation/{id}/{card_detail}', 'Repo\PaymentGateway\Wonderland@confirmation')->name('wonderland-confirmation');
Route::post('wonderland/notification/{id}', 'Repo\PaymentGateway\Wonderland@notification')->name('wonderland-notification');
Route::get('wonderland/callback/{id}', 'Repo\PaymentGateway\Wonderland@callback')->name('wonderland-callback');
Route::get('wonderlandvisa/pendingBlade/{id}/{card_detail}', 'Repo\PaymentGateway\WonderlandVisa@pendingBlade')->name('wonderlandvisa.pendingBlade');
Route::get('wonderlandvisa/return/{id}', 'Repo\PaymentGateway\WonderlandVisa@return')->name('wonderlandvisa.return');
Route::post('wonderlandvisa/notify/{id}', 'Repo\PaymentGateway\WonderlandVisa@notify')->name('wonderlandvisa.notify');

// SecureEPayment webhook call
Route::get('notification/secure-epayment', 'Repo\PaymentGateway\SecureePayment@notification')->name('notification-secure-epayment');
Route::post('notification/secure-epayment', 'Repo\PaymentGateway\SecureePayment@postnotification')->name('notification-secure-epayment-post');

//SecureePayments
Route::get('secureepayments/input/{session_id}', 'Repo\PaymentGateway\SecureePayment@inputResponse')->name('secureepayments-inputResponse');
Route::get('secureepayments/callback', 'Repo\PaymentGateway\SecureePayment@callBack')->name('secureepayments-callback-url');
Route::post('secureepayments/webhook', 'Repo\PaymentGateway\SecureePayment@webHook')->name('secureepayments-webhook-url');

// Stanbic
Route::get('stanbic/callback/{id}', 'Repo\PaymentGateway\Stanbic@callBack')->name('stanbic.callback');

//Altercards Route
Route::get('altercards/callback', 'Repo\PaymentGateway\Altercards@callBack')->name('altercards.callback');
Route::post('altercards/webhook', 'Repo\PaymentGateway\Altercards@webhook')->name('altercards.webhook');



//Basqet
Route::get("basqet/initialize/{id}", "Repo\PaymentGateway\Basqet@initialize")->name("basqet.initialize");
Route::post("basqet/verify", "Repo\PaymentGateway\Basqet@verify")->name("basqet.verify");
Route::get("basqet/back/{id}", "Repo\PaymentGateway\Basqet@back")->name("basqet.back");
Route::get("basqet/declined/{str}", "Repo\PaymentGateway\Basqet@declined")->name("basqet.declined");
Route::get("basqet/pending/{id}", "Repo\PaymentGateway\Basqet@pending")->name("basqet.pending");
Route::get("basqet/success/{id}", "Repo\PaymentGateway\Basqet@success")->name("basqet.success");
Route::post('basqet/payment-received', 'Repo\PaymentGateway\Basqet@paymentReceived')->name('basqet-webhook-url');

// AIGlobal Pay
Route::any('aiglobalpay/return/{id}', 'Repo\PaymentGateway\AIGlobalPay@return')->name("aiglobalpay.return");
Route::post("aiglobalpay/notify/{id}", 'Repo\PaymentGateway\AIGlobalPay@notify')->name("aiglobalpay.notify");

// Xchange
Route::any('xchange/return/{id}', 'Repo\PaymentGateway\Xchange@return')->name("xchange.return");
Route::post("xchange/notify/{id}", 'Repo\PaymentGateway\Xchange@notify')->name("xchange.notify");

//CarpPay
Route::post('carppay/notify/{id}', "Repo\PaymentGateway\Carppay@notify")->name("carppay.notify");

//RoyalPay
Route::any('royalpay/callback/{id}', 'Repo\PaymentGateway\Royalpay@callback')->name('royalpay-callback');
Route::any('royalpay/success/{id}', 'Repo\PaymentGateway\Royalpay@success')->name('royalpay-success');
Route::any('royalpay/fail/{id}', 'Repo\PaymentGateway\Royalpay@fail')->name('royalpay-fail');
Route::any('royalpay/pending/{id}', 'Repo\PaymentGateway\Royalpay@pending')->name('royalpay-pending');

//Payecards
Route::get('payecards/return/{id}', 'Repo\PaymentGateway\PayeCards@return')->name('payecards.return');
Route::any('payecards/redirect', 'Repo\PaymentGateway\PayeCards@redirect')->name('payecards.redirect');
Route::post('payecards/notify', 'Repo\PaymentGateway\PayeCards@notify')->name('payecards.notify');

/********************* Gateway route End *************************************/


Route::get('downloadFilesUploaded', 'FilesUploadController@downloadFilesUploaded')->name('downloadFilesUploaded');
Route::get('viewFilesUploaded', 'FilesUploadController@viewFilesUploaded')->name('viewFilesUploaded');

// iframe v1
Route::get('payment/{token}', 'iFrameCheckoutController@index')->name('iframe.checkout');
Route::post('submit_form/{token}', 'iFrameCheckoutController@store')->name('checkout-form');
Route::get('response/{token}', 'iFrameCheckoutController@hostedCheckoutResponse')->name('hosted-checkout-response');
Route::get('testRequest', 'iFrameCheckoutController@testRequest')->name('testRequest');
Route::get('iframe-checkout-cancel/{session_id}', 'iFrameCheckoutController@checkoutCancel')->name('iframe-checkout-cancel');

// iframe 2
Route::get('v2/iframe-checkout/{token}', 'iFrameTwoController@index')->name('iframe2.checkout');
Route::post('v2/checkout-form/{token}', 'iFrameTwoController@store')->name('iframe2.checkout-form');
Route::get('v2/iframe/response/{token}', 'iFrameTwoController@response')->name('iframe2.response');

// API document
Route::get('guide', 'APIDocumentController@index')->name('api-document');
// Route::get('user-api/directpayapi', 'UserAPIController@directpayapi')->name('directpayapi');
Route::get('guide/payment', 'UserAPIController@directpayapiv2')->name('directpayapiv2');
// Route::get('user-api/refundtransactionapi', 'UserAPIController@refundtransactionapi')->name('refundtransactionapi');
// Route::get('user-api/card-tokenization-api', 'UserAPIController@cardtokenizationapi')->name('cardtokenizationapi');
Route::get('guide/transactiondetailsapi', 'UserAPIController@gettransactiondetailsapi')->name('gettransactiondetailsapi');
// Route::get('user-api/hostedpayapi', 'UserAPIController@hostedpayapi')->name('hostedpayapi');
// Route::get('user-api/cryptopayapi', 'UserAPIController@cryptopayapi')->name('cryptopayapi');
// Route::get('user-api/bankpayapi', 'UserAPIController@bankpayapi')->name('bankpayapi');

//simplepay-fail
Route::get('simplepay/success/{id}', 'Repo\PaymentGateway\Simplepay@success')->name('simplepay-success');
Route::get('simplepay/fail/{id}', 'Repo\PaymentGateway\Simplepay@fail')->name('simplepay-fail');

//Senmo-Route
Route::get('senmo/confirmation/{id}', 'Repo\PaymentGateway\Senmo@confirmation')->name('senmo-confirmation');
Route::get('senmo/success/{id}', 'Repo\PaymentGateway\Senmo@success')->name('senmo-success');
Route::get('senmo/fail/{id}', 'Repo\PaymentGateway\Senmo@fail')->name('senmo-fail');

// paygenius
Route::get('pay-genius/confirm-payment/{id}', 'Repo\PaymentGateway\PayGenius@confirmPayment')->name('payGenius.confirmPayment');
Route::post('pay-genius/redirect/{id}', 'Repo\PaymentGateway\PayGenius@redirect')->name('payGenius.redirect');
Route::post('pay-genius/notify/{id}', 'Repo\PaymentGateway\PayGenius@notify')->name('payGenius.notify');

// FCFPay
Route::get("fcfpay/redirect/{id}", "Repo\PaymentGateway\FCFPay@redirect")->name("fcfpay.redirect");
Route::post("fcfpay/callback", "Repo\PaymentGateway\FCFPay@callback")->name("fcfpay.callback");

// api v2 checkout page
// Route::get('seamless/payment/checkout/{id}', 'API\ApiController@checkout')->name('api.v2.checkout');

// api v2 payment type select view
Route::get('seamless/payment/card/{id}', 'API\ApiController@card')->name('api.v2.card');
// Route::get('api/v2/select/bank/{id}', 'API\ApiController@bank')->name('api.v2.bank');
// Route::get('api/v2/select/crypto/{id}', 'API\ApiController@crypto')->name('api.v2.crypto');
// Route::get('api/v2/select/upi/{id}', 'API\ApiController@upi')->name('api.v2.upi');

// card process page
// Route::post('api/v2/card-type/select/{id}', 'API\ApiController@cardSelect')->name('api.v2.cardSelect');
// Route::post('api/v2/validate/card-details/{id}', 'API\ApiController@liveAjaxValidation')->name('api.v2.liveAjaxValidation');
Route::post('seamless/payment/submit/{id}', 'API\ApiController@extraDetailsFormSubmit')->name('api.v2.extraDetailsFormSubmit');

// bank process routes
// Route::post('api/v2/submit/bank/{id}', 'API\ApiController@bankSubmit')->name('api.v2.bankSubmit');
// Route::get('api/v2/test-3ds/bank/{id}', 'API\ApiController@testBank3DS')->name('api.v2.testBank3DS');
// Route::post('api/v2/test-3ds/submit/bank/{id}', 'API\ApiController@testBank3DSSubmit')->name('api.v2.testBank3DSSubmit');

// crypto process routes
// Route::post('api/v2/submit/crypto/{id}', 'API\ApiController@cryptoSubmit')->name('api.v2.cryptoSubmit');
// Route::get('api/v2/test-3ds/crypto/{id}', 'API\ApiController@testCrypto3DS')->name('api.v2.testCrypto3DS');
// Route::post('api/v2/test-3ds/crypto/{id}', 'API\ApiController@testCrypto3DSSubmit')->name('api.v2.testCrypto3DSSubmit');

// upi process routes
// Route::post('api/v2/submit/upi/{id}', 'API\ApiController@upiSubmit')->name('api.v2.upiSubmit');

// success response page
Route::get('transaction/response/success/{id}', 'API\ApiController@success')->name('api.v2.success');

// decline response page
Route::get('transaction/response/decline/{id}', 'API\ApiController@decline')->name('api.v2.decline');
Route::get('transaction/response/blocked/{id}', 'API\ApiController@blocked')->name('api.v2.block');
Route::get('transaction/response/redirect-merchant/{id}', 'API\ApiController@redirect')->name('api.v2.redirect');

/********************* test apiv2 **************************/
// checkout page
Route::get('api/v2/test-checkout/{id}', 'API\TestApiController@checkout')->name('api.v2.test-checkout');
// api v2 payment type select view
Route::get('api/v2/select/test-card/{id}', 'API\TestApiController@card')->name('api.v2.test-card');
Route::get('api/v2/select/test-bank/{id}', 'API\TestApiController@bank')->name('api.v2.test-bank');
Route::get('api/v2/select/test-crypto/{id}', 'API\TestApiController@crypto')->name('api.v2.test-crypto');
Route::get('api/v2/select/test-upi/{id}', 'API\TestApiController@upi')->name('api.v2.test-upi');
// card process page
Route::post('api/v2/card-type/test-select/{id}', 'API\TestApiController@cardSelect')->name('api.v2.test-cardSelect');
Route::post('api/v2/submit/test-card-details/{id}', 'API\TestApiController@extraDetailsFormSubmit')->name('api.v2.test-extraDetailsFormSubmit');
// bank process routes
Route::post('api/v2/submit/test-bank/{id}', 'API\TestApiController@bankSubmit')->name('api.v2.test-bankSubmit');
Route::get('api/v2/test-3ds/test-bank/{id}', 'API\TestApiController@testBank3DS')->name('api.v2.test-testBank3DS');
Route::post('api/v2/test-3ds/submit/test-bank/{id}', 'API\TestApiController@testBank3DSSubmit')->name('api.v2.test-testBank3DSSubmit');
// crypto process routes
Route::post('api/v2/submit/test-crypto/{id}', 'API\TestApiController@cryptoSubmit')->name('api.v2.test-cryptoSubmit');
Route::get('api/v2/test-3ds/test-crypto/{id}', 'API\TestApiController@testCrypto3DS')->name('api.v2.test-testCrypto3DS');
Route::post('api/v2/test-3ds/test-crypto/{id}', 'API\TestApiController@testCrypto3DSSubmit')->name('api.v2.test-testCrypto3DSSubmit');
// upi process rotues
Route::post('api/v2/submit/test-upi/{id}', 'API\TestApiController@testUPISubmit')->name('api.v2.testUPISubmit');
// success response page
Route::get('api/v2/response/test-success/{id}', 'API\TestApiController@success')->name('api.v2.test-success');
// decline response page
Route::get('api/v2/response/test-decline/{id}', 'API\TestApiController@decline')->name('api.v2.test-decline');
Route::get('api/v2/response/test-redirect-merchant/{id}', 'API\TestApiController@redirect')->name('api.v2.test-redirect');
/******************* test apiv2 End ************************/

Route::post("transactworld/callback/{id}", "Repo\PaymentGateway\Transactworld@callback")->name("transactworld.callback");

Route::post("ezipay/callback", "Repo\PaymentGateway\Ezipay@callback")->name("ezipay.callback");
Route::get('ezipay/return/{id}', "Repo\PaymentGateway\Ezipay@return")->name("ezipay.return");

Route::get('qartpay/form/{id}', 'Repo\PaymentGateway\QartPays2s@form')->name("qartpay.form");
Route::post('qartpay/submit/{id}', 'Repo\PaymentGateway\QartPays2s@formSubmit')->name("qartpay.submit");
Route::post('qartpays2s/callback/{id}', 'Repo\PaymentGateway\QartPays2s@callbacks2s')->name('qartpays2s.callback');
Route::post('qartpays2s/formSendData/{id}', 'Repo\PaymentGateway\QartPays2s@formSendData')->name("qartpays2s.formSendData");

Route::get('qartpay/form/upi/{id}', 'Repo\PaymentGateway\QartPayUPI@form')->name("qartpayupi.form");
Route::post('qartpay/callback/upi/{id}', 'Repo\PaymentGateway\QartPayUPI@callback')->name("qartpayupi.callback");

Route::post('gtpay/callback', 'Repo\PaymentGateway\GTPay@callback')->name("gtpay.callback");
Route::get('gtpay/return', 'Repo\PaymentGateway\GTPay@return')->name("gtpay.return");


// Peach-Route
Route::get('peach/form/{id}', 'Repo\PaymentGateway\Peachpayments@peachPayForm')->name('peach-form');
Route::get('peach/callback/{id}', 'Repo\PaymentGateway\Peachpayments@callback')->name('peach-callback');

Route::get('payment/stripes/{session_id}', 'Repo\PaymentGateway\Stripes@stripeForm')->name('return-stripe');

// budpay
Route::get('budpay/callback/{id}', 'Repo\PaymentGateway\BudPay@callback')->name("budpay.callback");
Route::get('budpay/webhook', 'Repo\PaymentGateway\BudPay@webhook')->name("budpay.webhook");

// boombill
Route::get("boombill/redirect/{id}", "Repo\PaymentGateway\BoomBill@redirect")->name("boombill.redirect");
Route::post("boombill/webhook/{id}", "Repo\PaymentGateway\BoomBill@webhook")->name("boombill.webhook");

// korapay
Route::get("kora-pay/redirect/{id}", "Repo\PaymentGateway\Korapay@redirect")->name("korapay.redirect");
Route::get("kora-pay/pin/{id}", "Repo\PaymentGateway\Korapay@pin")->name("korapay.pin");
Route::post("kora-pay/pin-submit/{id}", "Repo\PaymentGateway\Korapay@pinSubmit")->name("korapay.pinSubmit");
Route::get("kora-pay/otp/{id}", "Repo\PaymentGateway\Korapay@otp")->name("korapay.otp");
Route::post("kora-pay/otp-submit/{id}", "Repo\PaymentGateway\Korapay@otpSubmit")->name("korapay.otpSubmit");
Route::post("kora-pay/webhook/{id}", "Repo\PaymentGateway\Korapay@webhook")->name("korapay.webhook");

// MilkyPay
Route::get("milkypay/back/{id}", "Repo\PaymentGateway\MilkyPay@return")->name("milkypay.return");
Route::get("milkypay/form/{id}", "Repo\PaymentGateway\MilkyPay@form")->name("milkypay.form");
Route::post("milkypay/callback/{id}", "Repo\PaymentGateway\MilkyPay@callback")->name("milkypay.callback");
Route::get("/milkypay/checkout/{id}", "Repo\PaymentGateway\MilkyPay@getBrowserInfo")->name('milkypay.getBrowser.info');
Route::post("/milkypay/store/browser/info", "Repo\PaymentGateway\MilkyPay@storeBrowserInfo")->name('milkypay.storeBrowser.info');
Route::get("/milkypay/pending-txn-job", "Repo\PaymentGateway\MilkyPay@pendingTxn");

Route::get('cronjob-honeypay-pending', 'CronJobController@honeypayPendingTransactionStatusChange')->name('cronjob-honeypay-pending');
Route::get('honeypay-pending-transaction', 'CronJobController@pendingTransactionStatusChangeHoneypay')->name('honeypay-pending-transaction');
Route::get('honeypay-pendingdays-transaction', 'CronJobController@pendingdaysTransactionStatusChangeHoneypay')->name('honeypay-pendingdays-transaction');

// Everpay
Route::get("everpay/return/{id}", "Repo\PaymentGateway\Everpay@return")->name("everpay.return");
Route::get("everpay/form/{id}", "Repo\PaymentGateway\Everpay@form")->name("everpay.form");
Route::post("everpay/callback/{id}", "Repo\PaymentGateway\Everpay@callback")->name("everpay.callback");
Route::get('cronjob-everpay-pending', 'CronJobController@everpayPendingTransactionStatusChange')->name('cronjob-everpay-pending');
Route::get('everpay-pending-transaction', 'CronJobController@pendingTransactionStatusChangeEverpay')->name('everpay-pending-transaction');
Route::get('everpay-pendingdays-transaction', 'CronJobController@pendingdaysTransactionStatusChangeEverpay')->name('everpay-pendingdays-transaction');

//AML Node
Route::get("amlnode/initialize/{id}", "Repo\PaymentGateway\AMLNode@initialize")->name("amlnode.initialize");
Route::post("amlnode/verify", "Repo\PaymentGateway\AMLNode@verify")->name("amlnode.verify");
Route::get("amlnode/back/{id}", "Repo\PaymentGateway\AMLNode@back")->name("amlnode.back");
Route::get("amlnode/declined/{str}", "Repo\PaymentGateway\AMLNode@declined")->name("amlnode.declined");
Route::get("amlnode/pending/{id}", "Repo\PaymentGateway\AMLNode@pending")->name("amlnode.pending");
Route::get("amlnode/success/{id}", "Repo\PaymentGateway\AMLNode@success")->name("amlnode.success");
Route::post("amlnode/callback", "Repo\PaymentGateway\AMLNode@callback")->name("amlnode.callback");

//Fibonatix
Route::post("fibonatix/redirect/{id}", "Repo\PaymentGateway\Fibonatix@redirect")->name("fibonatix.redirect");
Route::post("fibonatix/callback/{id}", "Repo\PaymentGateway\Fibonatix@callback")->name("fibonatix.callback");

//4on
Route::get("4on/redirect/{id}", "Repo\PaymentGateway\FourON@redirect")->name("FourON.redirect");
Route::post("4on/notify/{id}", "Repo\PaymentGateway\FourON@notify")->name("FourON.notify");

// Aron3ds
Route::get("aron/redirect/{id}", "Repo\PaymentGateway\Aron3ds@redirect")->name("aron.redirect");

Route::any('virtualpay/pending/{id}', 'Repo\PaymentGateway\VirtualPay@pendingBlade')->name('virtualpay.pendingBlade');
Route::get("virtualpay/redirect/{id}", "Repo\PaymentGateway\VirtualPay@redirect")->name("virtualpay.redirect");

// Cron job route only
// currency rates table
Route::get('get/currency-rates', 'CronJobController@getCurrencyRate')->name('cronjob.getCurrencyRate');
Route::get('restore-session-transaction', 'CronJobController@restoreSessionTransaction')->name('cronjob.restoreSessionTransaction');
Route::get('cronjob-opay', 'CronJobController@opayPendingTransactionStatusChange')->name('cronjob-opay');
Route::get('cronjob-chakra', 'CronJobController@chakraPendingTransactionStatusChange')->name('cronjob-chakra');

Route::get('cronjob-for-update-test-transnaction', 'CronJobController@pendingTransactionStatusChange')->name('cronjob-for-update-test-transnaction');
Route::get('cronjob-ezipay', 'CronJobController@pendingEzipayTransactionStatusChange')->name('cronjob-ezipay');
Route::get('success-cronjob-ezipay', 'CronJobController@successEzipayTransactionStatusChange')->name('success-cronjob-ezipay');
Route::get('cronjob-stanbic', 'CronJobController@pendingStanbicTransactionStatusChange')->name('cronjob-stanbic');

Route::get('stanbic-checking', 'CronJobController@checkOrder')->name("stanbic-checking");


Route::get('cronjob-qikpay', 'CronJobController@pendingqikpayTransactionStatusChange')->name('cronjob-qikpay');

Route::get('cronjob-avalanche', 'CronJobController@pendingAvalancheTransactionStatusChange')->name('cronjob-avalnche');



Route::get('cronjob-boombill', 'CronJobController@pendingBoombillTransactionStatusChange')->name('cronjob-boombill');


Route::get('soi/redirect/{id}', 'Repo\PaymentGateway\Soi@Redirect')->name("soi.redirect");
Route::post('soi/webhook/{id}', 'Repo\PaymentGateway\Soi@Webhook')->name("soi.webhook");

Route::get('soihosted/redirect/{id}', 'Repo\PaymentGateway\Soihosted@Redirect')->name("soihosted.redirect");
Route::post('soihosted/webhook/{id}', 'Repo\PaymentGateway\Soihosted@Webhook')->name("soihosted.webhook");

Route::get('secure-gateway/redirect/{id}', 'Repo\PaymentGateway\Texcent@redirect')->name('texCent.redirect');

Route::get("paypound/response/{id}", "Repo\PaymentGateway\Paypound@redirect")->name("paypound.response");
Route::post("paypound/webhook/{id}", "Repo\PaymentGateway\Paypound@webhook")->name("paypound.webhook");

Route::get('cronjob-paypound', 'CronJobController@pendingPaypoundTransactionStatusChange')->name('cronjob-paypound');

Route::get("oculus/input-submit", "Repo\PaymentGateway\Oculus@inputSubmit")->name("oculus.input");
Route::get("oculus/callback", "Repo\PaymentGateway\Oculus@callBack")->name("oculus.callback");
Route::get("oculus/webhook", "Repo\PaymentGateway\Oculus@webhook")->name("oculus.webhook");
Route::get('cronjob-oculus-pending', 'CronJobController@oculusPendingTransactionStatusChange')->name('cronjob-oculus-pending');
Route::get('cronjob-oculus-pending-to-decline', 'CronJobController@oculusPendingTransactionStatusToDecline')->name('cronjob-oculus-pending-to-decline');

// coingate
Route::post('coingate-callbackUrl/{sessiondata}', 'Repo\PaymentGateway\TestCoingate@callbackUrl')->name('coingate-callbackUrl');
Route::get('coingate-successUrl/{sessiondata}', 'Repo\PaymentGateway\TestCoingate@successUrl')->name('coingate-successUrl');
Route::get('coingate-cancelUrl/{sessiondata}', 'Repo\PaymentGateway\TestCoingate@cancelUrl')->name('coingate-cancelUrl');

// The Paying Spot Mid Routes
Route::post('thepayingspot/callback/{sessiondata}', 'Repo\PaymentGateway\Thepayingspot@webhookDetails')->name('thepayingspot.callback');
Route::get('thepayingspot/{sessiondata}', 'Repo\PaymentGateway\Thepayingspot@redirectUrl')->name('thepayingspot.return');
Route::get('/thepayingspot-cron', 'Repo\PaymentGateway\Thepayingspot@restoreTransactions');

// The AttitudePay MID Routes
Route::get("attitudepay/callback/{orderId}", 'Repo\PaymentGateway\AttitudePay@webhook')->name('attitudepay.callback');
Route::post("attitudepay/{orderId}", 'Repo\PaymentGateway\AttitudePay@redirect')->name('attitudepay.redirect');

// CaresPay MID Routes
Route::post("carespay/{orderId}", "Repo\PaymentGateway\CaresPay@redirect")->name("carespay.redirect");
Route::post("carespay/callback/{orderId}", "Repo\PaymentGateway\CaresPay@webhook")->name("carespay.webhook");

// Kryptova MID Routes
Route::get("cryp/response/{id}", "Repo\PaymentGateway\Kryptova@redirect")->name("kryptova.response");
Route::post("cryp/webhook/{id}", "Repo\PaymentGateway\Kryptova@webhook")->name("kryptova.webhook");
Route::get("cryp/pending/txn", "Repo\PaymentGateway\Kryptova@updatePendingTx");

// CentPays MID Routes
Route::post("centpays/callback/{orderId}", "Repo\PaymentGateway\CentPays@redirect")->name("centpays.callback");
Route::match(["get", 'post'], "centpays/webhook", "Repo\PaymentGateway\CentPays@webhhok")->name("centpays.webhook");
Route::get("centpay/transaction-cron", "Repo\PaymentGateway\CentPays@restoreTransactions");

// TomiPay MID Routes
Route::get("tomipay/webhook/{orderId}", 'Repo\PaymentGateway\TomiPay@webhook')->name('tomipay.webhook');
Route::post("tomipay/{orderId}", 'Repo\PaymentGateway\TomiPay@redirect')->name('tomipay.redirect');

//  BMAG MID routes
Route::get("bmag/{orderId}", "Repo\PaymentGateway\BMAG@redirect")->name("bmag.redirect");

// Zoftpay MID routes
Route::any("zoftpay/callback/{orderId}", "Repo\PaymentGateway\ZoftPay@callback")->name("zoftpay.callback");
Route::any("zoftpay/webhook/{orderId}", "Repo\PaymentGateway\ZoftPay@webhook")->name("zoftpay.webhook");

// Admin auto mid volume report mail
Route::get('admin/mid-volume-mail', 'Admin\AdminAutoMidVolumeController@sendAutoPayoutMail');

// * Send the Jobs table count route
Route::get("/jobs-count", 'Admin\AdminTestController@sendJobsCount');

// * Symoco mid routes
Route::get('/symoco-initialPage/{id}/{card}', 'Repo\PaymentGateway\Symoco@initialPage')->name('symoco.initialPage');
Route::post('/symoco-3ds', 'Repo\PaymentGateway\Symoco@authPage')->name('symoco.authPage');
Route::post('/symoco-fingerprint-error', 'Repo\PaymentGateway\Symoco@fingerprintError')->name('symoco.fingerprint.error');
Route::get('/symoco-redirect/{id}', 'Repo\PaymentGateway\Symoco@redirect')->name('symoco.redirect');

// * CashEnvoy Mid routes
Route::get("/cashenvoy-return/{id}", 'Repo\PaymentGateway\CashEnvoy@returnCallback')->name("cashenvoy.return");
Route::post("/cashenvoy-webhook/{id}", 'Repo\PaymentGateway\CashEnvoy@webhook')->name("cashenvoy.webhook");

// * InfiPay
Route::get("/infipay-redirect/{id}", "Repo\PaymentGateway\InfiPay@returnCallback")->name('infipay-return');
Route::get("/infipay-bank-select/{id}/{currency}", "Repo\PaymentGateway\InfiPay@selectBank")->name('infipay-bank-select');
Route::post("/infipay-bank-select-store", "Repo\PaymentGateway\InfiPay@selectBankStore")->name('infipay-bank-select-store');
Route::post('/infipay-webhook', "Repo\PaymentGateway\InfiPay@webhook")->name('infipay-webhook');

// * WinoPay Mid routes
Route::get("winopay/return/{id}", "Repo\PaymentGateway\WinoPay@return")->name("winopay.return");
Route::post("winopay/callback", "Repo\PaymentGateway\WinoPay@callback")->name("winopay.callback");

// * Payzentric routes
Route::get("/payzentric/return/{id}", "Repo\PaymentGateway\Payzentric@return")->name("payzentric.return");
Route::post("/payzentric/webhook", "Repo\PaymentGateway\Payzentric@webhook")->name("payzentric.webhook");

// * Payaza routes
Route::get('/payaza/payment/{id}', "Repo\PaymentGateway\Payaza@payaza3ds")->name("payaza.3ds");
Route::any('/payaza/callback/{id}', "Repo\PaymentGateway\Payaza@payazaReturn")->name("payaza.return");
// Route::get('/payaza-cron', "Repo\PaymentGateway\Payaza@payazaCron")->name("payaza.cron");
// Route::get("/payaza-pending-declined", "Repo\PaymentGateway\Payaza@declinedOldPendingTxn")->name('payaza.pending.declined');

// * Highisk MID roites
Route::get("/highisk/return/{id}", "Repo\PaymentGateway\Highisk@return")->name("highisk.return");
Route::get("/highisk/webhook/{id}", "Repo\PaymentGateway\Highisk@webhook")->name("highisk.webhook");
Route::get("/highisk/pending-txn-restore", "Repo\PaymentGateway\Highisk@restorePendingTxn");

// * KiwiPay MID routes
Route::get("/kiwipay/success", "Repo\PaymentGateway\KiwiPay@success")->name("kiwipay.success");
Route::get("/kiwipay/error", "Repo\PaymentGateway\KiwiPay@success")->name("kiwipay.error");
Route::post("/kiwipay/webhook", "Repo\PaymentGateway\KiwiPay@webhook")->name("kiwipay.webhook");

// * yeloPay Routes
Route::any("/yelopay/return/{id}", "Repo\PaymentGateway\YeloPay@return")->name("yelopay.return");
Route::get("/yelopay/3ds/{id}", "Repo\PaymentGateway\YeloPay@redirectForm")->name("yelopay.3ds");
Route::post("/yelopay/3ds/callback", "Repo\PaymentGateway\YeloPay@callback")->name("yelopay.3ds.callback");
Route::get("/yelopay/pending-txn-job", "Repo\PaymentGateway\YeloPay@pendingTxnJob");

//Leonepay
Route::get("/leonepay/redirect/{id}", "Repo\PaymentGateway\LeonePay@redirect")->name("leonepay.redirect");
Route::post("/leonepay/webhook/{id}", "Repo\PaymentGateway\LeonePay@webhook")->name("leonepay.webhook");
Route::get("/leonepay/pending-txn-job", "Repo\PaymentGateway\LeonePay@pendingTxnJob");

// Redfern routes
Route::get("/redfern/return/{id}", "Repo\PaymentGateway\Redfern@return")->name("redfern.return");
Route::any("/redfern/callback/{id}", "Repo\PaymentGateway\Redfern@callback")->name("redfern.callback");

// monnet routes
Route::get('monnet/payment/option/{id}/{country}', 'Repo\PaymentGateway\Monnet@paymentOptionForm')->name('monnet.payment.option.form');
Route::post('monnet/payment/option', 'Repo\PaymentGateway\Monnet@paymentOptionFormSubmit')->name('monnet.payment.option.form.submit');
Route::any('monnet/redirect/{id}', 'Repo\PaymentGateway\Monnet@redirect')->name('Monnet.redirect');
Route::post('monnet/callback/receive', 'Repo\PaymentGateway\Monnet@notify')->name('Monnet.notify');

// * Dasshpe
Route::post("/dasshpe/return/{id}", "Repo\PaymentGateway\Dasshpe@return")->name("dasshpe.return");
Route::get("/dasshpe/auth/form/{id}", "Repo\PaymentGateway\Dasshpe@authForm")->name('dasshpe.auth.form');

// * DasshpeUPI
Route::get("/dasshpeupi/form/{id}", "Repo\PaymentGateway\Dasshpeupi@form")->name("dasshpeupi.form");
Route::post("/dasshpeupi/return/{id}", "Repo\PaymentGateway\Dasshpeupi@return")->name("dasshpeupi.return");

// * Opac MID routes
Route::get("/opacauthurl/{id}", "Repo\PaymentGateway\Opac@browserInfo")->name("opac.browser_info");
Route::post("/opac/store/browser-info", "Repo\PaymentGateway\Opac@storeBrowserInfo")->name("opac.store.browser_info");
Route::get("/opac/return/{id}", "Repo\PaymentGateway\Opac@return")->name("opac.return");

// * MekaPay Routes
Route::get("/mekapay/callback/{id}", "Repo\PaymentGateway\MekaPay@callback")->name("mekapay.callback");
Route::get("/mekapay/pending-txn-job", "Repo\PaymentGateway\MekaPay@pendingTxnJob");

// * EMS route
Route::any("ems/return/{id}", "Repo\PaymentGateway\EmsCardStream@return")->name('ems.return');
Route::post("ems/callback/{id}", "Repo\PaymentGateway\EmsCardStream@callback")->name('ems.callback');
Route::get("/ems/form/{id}", "Repo\PaymentGateway\EmsCardStream@form")->name("ems.form");

// Coinspaid
Route::get('coinspaid/success/{id}', 'Repo\PaymentGateway\CoinsPaid@success')->name("coinspaid-success");
Route::get('coinspaid/failed/{id}', 'Repo\PaymentGateway\CoinsPaid@fail')->name("coinspaid-failed");
Route::any('coinspaid/webhook/KTOIJcrRPKVhtVTYKMWaTWYxuk7wmB', 'Repo\PaymentGateway\CoinsPaid@webhook');

// * 3xGate urls
Route::get("3xgate/success/{id}", "Repo\PaymentGateway\Gate3x@success")->name('gate3x.success');
Route::get("3xgate/fail/{id}", "Repo\PaymentGateway\Gate3x@fail")->name('gate3x.fail');
Route::get("3xgate/pending/{id}", "Repo\PaymentGateway\Gate3x@pending")->name('gate3x.pending');
Route::get("3xgate/cancel/{id}", "Repo\PaymentGateway\Gate3x@cancel")->name('gate3x.cancel');

// * Fnpo Routes
Route::get("/fnpo/redirect/{id}", "Repo\PaymentGateway\Fnpo@redirect")->name("fnpo.redirect");
Route::post("/fnpo/webhook/{id}", "Repo\PaymentGateway\Fnpo@webhook")->name("fnpo.webhook");

// * Payment page local changes 
Route::get("change-lang", "API\LanaguageController@changeLang")->name("change.lang");

// itexpay
Route::get('itexpay/redirect/{id}', 'Repo\PaymentGateway\Itexpay@redirect')->name('itexpay.redirect');
Route::get('itexpay/form/{id}/{encrypt}', 'Repo\PaymentGateway\Itexpay@form')->name('itexpay.form');
Route::post('itexpay/submit/{id}/{encrypt}', 'Repo\PaymentGateway\Itexpay@submit')->name('itexpay.submit');
Route::post('itexpay/callback/receive', 'Repo\PaymentGateway\Itexpay@callback')->name('itexpay.callback');


// * Arca Payments 
Route::get("/arca/redirect/{id}", "Repo\PaymentGateway\Arca@redirect")->name("arca.redirect");

// *Xamax MID urls
Route::any("cryptoxamax/callback", "Repo\PaymentGateway\CryptoXamax@callback")->name("xamax.callback");
Route::get("cryptoxamax/wallet/{id}", "Repo\PaymentGateway\CryptoXamax@showWallet")->name("xamax.show.wallet");
Route::get("/cryptoxamax/user/redirect/{id}", "Repo\PaymentGateway\CryptoXamax@userRedirect")->name("xamax.user.redirect");

// * Startbutton MID url
Route::get("startbutton/callback/{id}", "Repo\PaymentGateway\StartButton@callback")->name("startbutton.callback");
Route::any("startbutton/webhook", "Repo\PaymentGateway\StartButton@webhook");

// * Bitpace MID urls
Route::get("bitpace/callback/{id}", "Repo\PaymentGateway\Bitpace@callback")->name("bitpace.callback");
Route::get("bitpace/error/callback/{id}", "Repo\PaymentGateway\Bitpace@errorCallback")->name("bitpace.error.callback");

// * Facilero MID
Route::get("/faci/browser-info/{id}", "Repo\PaymentGateway\Facilero@browserInfo")->name("facilero.browser.info");
Route::post("/faci/browser-info", "Repo\PaymentGateway\Facilero@storeBrowserInfo")->name("facilero.store.browser.info");
Route::get("/faci/redirect-form/{id}", "Repo\PaymentGateway\Facilero@redirectForm")->name("facilero.redirect.form");
Route::post("/faci/redirect/{id}", "Repo\PaymentGateway\Facilero@redirect")->name("facilero.redirect");
Route::post("/faci/webhook/{id}", "Repo\PaymentGateway\Facilero@webhook")->name("facilero.webhook");

// * PivotPay
Route::get("/pivot-otp/{id}", "Repo\PaymentGateway\PivotPay@otpPage")->name("pivot.otp");
Route::post("/pivot-otp-store", "Repo\PaymentGateway\PivotPay@storeOtpPage")->name("store.pivot.otp");
Route::get("/pivot-pin/{id}", "Repo\PaymentGateway\PivotPay@pinPage")->name("pivot.pin");
Route::post("/pivot-pin-store", "Repo\PaymentGateway\PivotPay@storePinPage")->name("store.pivot.pin");
Route::get("/pivot/callback/{id}", "Repo\PaymentGateway\PivotPay@callback")->name('pivot.callback');


Route::any("/kpentag/redirect/{id}", "Repo\PaymentGateway\Kpentag@redirect")->name("kpentag.redirect");
Route::post("/kpentag/webhook/{id}", "Repo\PaymentGateway\Kpentag@webhook")->name("kpentag.webhook");

Route::get("uzopay/redirect/{id}", "Repo\PaymentGateway\Uzopay@redirect")->name("uzopay.response");
Route::post("uzopay/webhook/{id}", "Repo\PaymentGateway\Uzopay@webhook")->name("uzopay.webhook");

Route::get("securepay/return/{id}", "Repo\PaymentGateway\SecurePay@redirect")->name("securepay.return");
Route::post("securepay/webhook/{id}", "Repo\PaymentGateway\SecurePay@webhook")->name("securepay.webhook");


// PrismPay
Route::get("prismpay/form/{id}", "Repo\PaymentGateway\PrismPay@prismpayForm")->name("prismpay.form-request");
Route::post('prisampay/data', 'Repo\PaymentGateway\PrismPay@getPrisampayData')->name('prisampay.data');
Route::post('prisampay/fail', 'Repo\PaymentGateway\PrismPay@prisampayFail')->name('prisampay.fail');


// * Chargemoney MID urls
Route::get("chargemoney/redirect/{id}", "Repo\PaymentGateway\Chargemoney@redirect")->name("chargemoney.redirect");
Route::post("chargemoney/callback/{id}", "Repo\PaymentGateway\Chargemoney@callback")->name("chargemoney.callback");