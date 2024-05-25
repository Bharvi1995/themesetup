<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Agent Routes
|--------------------------------------------------------------------------
*/

Route::group(['middleware' => 'notification_read_agent'], function () {

	// Dashboard controller
	Route::get('dashboard', 'Agent\AgentUserBaseController@dashboard')->name('rp.dashboard');

	// Application controller
	// Route::get('verification', 'Agent\ApplicationController@create')->name('rp.my-application.create');
	// Route::get('my-application/detail', 'Agent\ApplicationController@detail')->name('rp.my-application.detail');
	// Route::post('my-application/store', 'Agent\ApplicationController@store')->name('rp.my-application.store');
	// Route::get('my-application/edit', 'Agent\ApplicationController@edit')->name('rp.my-application.edit');
	// Route::post('my-application/update', 'Agent\ApplicationController@update')->name('rp.my-application.update');
	Route::get('downloadDocumentsUploadRpApplication', 'Agent\ApplicationController@downloadDocumentsUploadRpApplication')->name('downloadDocumentsUploadRpApplication');

	Route::get('profile', 'Agent\AgentUserBaseController@profile')->name('profile-rp');
	Route::post('profile-update', 'Agent\AgentUserBaseController@updateProfile')->name('rp-profile-update');
	Route::get('merchants', 'Agent\AgentUserBaseController@getUserManagement')->name('rp.user-management');
	Route::get('user-management/{id}', 'Agent\AgentUserBaseController@show')->name('user-management-show');
	Route::post('user-deactive', 'Agent\AgentUserBaseController@userActiveDeactive')->name('user-deactive-for-rp');

	Route::get('merchant/create', 'Agent\UserManagementController@create')->name('user-management-agent-create');
	Route::post('user-management-store', 'Agent\UserManagementController@store')->name('user-management-agent-store');
	// Route::get('user-management-application-show/{id}', 'Agent\UserManagementController@applicationShow')->name('user-management-application-show');
	// Route::get('user-management-application-create/{id}', 'Agent\UserManagementController@applicationCreate')->name('user-management-application-create');
	// Route::post('user-management-application-store/{id}', 'Agent\UserManagementController@applicationsStore')->name('user-management-application-store');
	// Route::get('user-management-application-edit/{id}', 'Agent\UserManagementController@applicationEdit')->name('user-management-application-edit');
	// Route::put('user-management-application-update/{id}', 'Agent\UserManagementController@applicationsUpdate')->name('user-management-application-update');
	Route::get('downloadDocumentsUploadeUser', 'Agent\UserManagementController@downloadDocumentsUploade')->name('downloadDocumentsUploadeUser');

	//Merchant Transactions
	Route::get('merchant/payments', 'Agent\MerchantTransactionController@index')->name('rp-merchant-transactions');
	// Route::get('merchant-refund-transactions', 'Agent\MerchantTransactionController@refund')->name('rp-merchant-refund-transactions');
	// Route::get('merchant-marked-transactions', 'Agent\MerchantTransactionController@flagged')->name('rp-merchant-suspicious-transactions');
	// Route::get('merchant-chargebacks-transactions', 'Agent\MerchantTransactionController@chargebacks')->name('rp-merchant-chargebacks-transactions');
	// Route::get('merchant-retrieval-transactions', 'Agent\MerchantTransactionController@retrieval')->name('rp-merchant-retrieval-transactions');
	// Route::get('merchant-transactions/{id}', 'Agent\MerchantTransactionController@show')->name('rp-merchant-transactions-show');
	Route::post('merchant-transactions-details', 'Agent\MerchantTransactionController@transactionDetails')->name('rp.merchant-transactions-details');

	Route::get('report', 'Agent\ReportController@getReport')->name('rp.merchant.report');
	Route::get('payout-report', 'Agent\ReportController@getPayoutreport')->name('rp.merchant.payout.report');
	Route::get('payout-report/show/{id}', 'Agent\ReportController@showPayoutReport')->name('rp.merchant.payout.report.show');
	Route::get('payout-report/pdf/{id}', 'Agent\ReportController@getPayoutReportPdf')->name('rp.merchant.payout.report.pdf');
	Route::get('payout-report/excel', 'Agent\ReportController@payoutReportExcel')->name('rp.merchant.payout.report.excel');

	Route::get('rp-report', 'Agent\ReportController@agentReport')->name('rp.rp-report');
	Route::get('generate-rp-report', 'Agent\ReportController@generateAgentReport')->name('rp.generate-rp-report');
	Route::get('generate-rp-report/pdf/{id}', 'Agent\ReportController@getAgentreportPdf')->name('rp.generate.rp.report.pdf');
	Route::get('generate-rp-report/show/{id}', 'Agent\ReportController@showAgentreport')->name('generate.agent.report.show');
	Route::get('rp-summary-report', 'Agent\ReportController@summaryReport')->name('rp-summary-report');
	Route::get('rp-card-report', 'Agent\ReportController@cardSummaryReport')->name('rp.rp-card-report');
	Route::get('rp-payment-status-report', 'Agent\ReportController@paymentStatusReport')->name('rp.rp-payment-status-report');

	/************************Transaction Summary Report Route Start ***************************************/
	Route::get('transaction/report', 'Agent\ReportController@merchantTransactionsReport')->name('rp.merchant-transaction-report');
	Route::get('rp-merchant-transaction-report-excle', 'Agent\ReportController@merchantTransactionsReportExcle')->name('rp.rp-merchant-transaction-report-excle');
	/************************Transaction Summary Report Route End ***************************************/

	/************************Commition Report Route Start ***************************************/
	Route::get('commision/report', 'Agent\ReportController@commisionReport')->name('rp.commision-report');
	Route::get('risk-report', 'Agent\ReportController@riskReport')->name('rp.risk-report');
	/************************Commition Report Route End ***************************************/

	/************************Merchant Payout Report Route Start ***************************************/
	Route::get('rp-merchant-payout-report', 'Agent\ReportController@getRpMerchantPayoutReport')->name('rp.merchant-payout-report');
	Route::get('rp-merchant-payout_report/{id}', 'Agent\ReportController@RpMerchantPayoutReportshow')->name('rp.merchant_payout_report.show');
	Route::get('rp-merchant-payout_report/pdf/{id}', 'Agent\ReportController@RpMerchantPayoutReportgeneratePDF')->name('rp.merchant_payout_report.pdf');
	/************************Merchant Payout Report Route End ***************************************/

	// Route::get('bank-details', 'Agent\AgentUserBaseController@showBankDetails')->name('agent.bank.details');
	// Route::post('bank-details/store', 'Agent\AgentUserBaseController@updateBankDetail')->name('agent.bank.details.store');

	Route::post('get/transaction-overview', 'Agent\AgentUserBaseController@getTransactionOverview')->name('agent.transaction.overview');

	Route::get('notifications', 'Agent\NotificationController@notifications')->name('notifications');
	Route::get('read-notifications/{id}', 'Agent\NotificationController@readNotifications')->name('read-notifications');

	Route::resource('sub-rp', 'Agent\SubRpController');
});
