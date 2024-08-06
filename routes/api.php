<?php

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => ['api', 'cors']], function () {

	// new transaction API
	// Route::post('transaction', 'API\DirectApiController@store')->name('transaction');

	// test api
	Route::post('seamlesstest/transaction', 'API\TestDirectApiController@store')->name('test.transaction');

	// api v2
	Route::post('payment/order/create', 'API\ApiController@store')->name('transaction');

	// api v2 test
	// Route::post('v2/test/transaction', 'API\TestApiController@store')->name('v2.test-transaction');

	// hosted api
	// Route::post('hosted/transaction', 'API\HostedAPIController@store')->name('hostedAPI.store');

	// Test hosted api
	// Route::post('test/hosted/transaction', 'API\TestHostedAPIController@store')->name('test-hostedAPI.store');

	// get transaction detail
	Route::post('payment/order/details', 'API\DirectApiController@getTransaction')->name('get-transaction-details');

	//Refund API
	// Route::post('refund', 'API\DirectApiController@refund')->name('refund');

	// Crypto transaction api
	// Route::post('crypto/transaction', 'API\CryptoApiController@store')->name('crypto-transaction');

	//Bank transaction api
	// Route::post('bank/transaction', 'API\BankApiController@store')->name('bank-transaction');

	//Test crypto transaction api
	// Route::post('test/crypto/transaction', 'API\TestCryptoApiController@store')->name('test-crypto-transaction');

	//Test bank transaction api
	// Route::post('test/bank/transaction', 'API\TestBankApiController@store')->name('test-bank-transaction');

	// Card tokenizer api
	// Route::post('card/create/token', 'API\CardTokenizationController@index')->name('card-tokenization');
	// Route::post('card-tokenization-transaction', 'API\CardTokenizationController@store')->name('card-tokenization-transaction');

	// bank & agent api
	// Route::get('bank/register', 'API\BankController@register')->name('api.bank.register');
	// Route::post('bank/store', 'API\BankController@store')->name('api.bank.store');
	// Route::post('agent/store', 'API\AgentController@store')->name('api.agent.store');


	// * demo API
	// Route::post("/demo/tool", "API\TestDirectApiController@demoApi");

});