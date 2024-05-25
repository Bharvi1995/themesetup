<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| WL Agent Routes
|--------------------------------------------------------------------------
*/

// Dashboard controller
Route::get('dashboard', 'WLAgent\WLAgentUserBaseController@dashboard')->name('wl-dashboard');
Route::get('merchant-management', 'WLAgent\WLAgentUserBaseController@merchantManagement')->name('wl-merchant-management');
Route::post('show-user-details', 'WLAgent\WLAgentUserBaseController@showUserDetails')->name('show-wl-user-details');
Route::get('rate-fee', 'WLAgent\WLAgentUserBaseController@rateFee')->name('wl-rate-fee');

Route::get('profile', 'WLAgent\WLAgentUserBaseController@profile')->name('wl-profile-rp');
Route::post('profile-update', 'WLAgent\WLAgentUserBaseController@updateProfile')->name('wl-rp-profile-update');

Route::get('whitelist-ip', 'WLAgent\WLAgentUserBaseController@whiteListIp')->name('wl-rp-whitelist-ip');
Route::get('whitelist-ip-add', 'WLAgent\WLAgentUserBaseController@addWhiteListIp')->name('wl-rp-whitelist-ip-add');
Route::post('whitelist-ip-add-submit', 'WLAgent\WLAgentUserBaseController@addWhiteListSubmit')->name('wl-rp-whitelist-ip-add-submit');
Route::delete('wl-rp-deleteWebsiteUrl/{id}', 'WLAgent\WLAgentUserBaseController@deleteWebsiteUrl')->name('wl-rp-deleteWebsiteUrl');

Route::get('merchant-create', 'WLAgent\MerchantManagementController@create')->name('wl-merchant-create');
Route::post('merchant-store', 'WLAgent\MerchantManagementController@store')->name('wl-merchant-store');
Route::get('merchant-show/{id}', 'WLAgent\MerchantManagementController@show')->name('wl-merchant-show');
Route::get('merchant-edit/{id}', 'WLAgent\MerchantManagementController@edit')->name('wl-merchant-edit');
Route::put('merchant-update/{id}', 'WLAgent\MerchantManagementController@update')->name('wl-merchant-update');
Route::delete('merchant-destroy/{id}', 'WLAgent\MerchantManagementController@destroy')->name('wl-merchant-destroy');
Route::get('merchant-export', 'WLAgent\MerchantManagementController@export')->name('wl-merchant-export');
Route::get('downloadDocumentsUploadeWLUser', 'WLAgent\MerchantManagementController@downloadDocumentsUploade')->name('downloadDocumentsUploadeWLUser');

Route::get('merchant-transaction', 'WLAgent\MerchantTransactionController@allTransaction')->name('wl-merchant-transaction');
Route::get('merchant-crypto-transaction', 'WLAgent\MerchantTransactionController@cryptoTransaction')->name('wl-merchant-transaction-crypto');
Route::get('merchant-refund-transaction', 'WLAgent\MerchantTransactionController@refundTransaction')->name('wl-merchant-transaction-refund');
Route::get('merchant-chargebacks-transaction', 'WLAgent\MerchantTransactionController@chargebacksTransaction')->name('wl-merchant-transaction-chargebacks');
Route::get('merchant-marked-transaction', 'WLAgent\MerchantTransactionController@suspiciousTransaction')->name('wl-merchant-transaction-suspicious');
Route::get('merchant-declined-transaction', 'WLAgent\MerchantTransactionController@declinedTransaction')->name('wl-merchant-transaction-declined');
Route::get('merchant-retrieval-transaction', 'WLAgent\MerchantTransactionController@retrievalTransaction')->name('wl-merchant-transaction-retrieval');
Route::get('merchant-test-transaction', 'WLAgent\MerchantTransactionController@testTransaction')->name('wl-merchant-transaction-test');

Route::get('merchant-transaction/export', '\App\LazyCSVExport\WLRPMerchantTransactionCSVExport@download')->name('wl-rp-transactions-exportAllTransactions');
Route::get('merchant-crypto-transaction/export', '\App\LazyCSVExport\WLRPMerchantCryptoTransactionCSVExport@download')->name('wl-rp-crypto-transactions-export');
Route::get('merchant-refund-transaction/export', '\App\LazyCSVExport\WLRPMerchantRefundTransactionCSVExport@download')->name('wl-rp-refund-transactions-export');
Route::get('merchant-chargeback-transaction/export', '\App\LazyCSVExport\WLRPMerChantchargebackTransactionCSVExport@download')->name('wl-rp-chargeback-transactions-export');
Route::get('merchant-retrieval-transaction/export', '\App\LazyCSVExport\WLRPMerchantRetrievalTransactionCSVExport@download')->name('wl-rp-retrieval-transactions-export');
Route::get('merchant-suspicious-transaction/export', '\App\LazyCSVExport\WLRPMerchantSuspiciousTransactionCSVExport@download')->name('wl-rp-suspicious-transactions-export');
Route::get('merchant-declined-transaction/export', '\App\LazyCSVExport\WLRPMerchantDeclinedTransactionCSVExport@download')->name('wl-rp-declined-transactions-export');
Route::get('merchant-test-transaction/export', '\App\LazyCSVExport\WLRPMerchantTestTransactionCSVExport@download')->name('wl-rp-test-transactions-export');

// Transaction summery report
Route::get('transaction-summary-reports', 'WLAgent\ReportController@transactionSummaryReport')->name('wl-transaction-summary-reports');

/******************************** Summary Report Start **********************************************/
Route::get('summary-reports', 'WLAgent\ReportController@summaryReport')->name('wl-summary-reports');
Route::get('user-card-summary-report', 'WLAgent\ReportController@cardSummaryReport')->name('wl-user-card-summary-report');
Route::get('user-payment-status-summary-report', 'WLAgent\ReportController@paymentStatusSummaryReport')->name('wl-user-payment-status-summary-report');
/******************************** Summary Report End **********************************************/
Route::get('wl-payout-report', 'WLAgent\PayoutReportController@getPayoutReport')->name('wl-payout-report');
Route::get('wl-payout_report/pdf/{id}', 'WLAgent\PayoutReportController@generatePDF')->name('wl-payout_report.pdf');
Route::get('wl-payout_report/{id}', 'WLAgent\PayoutReportController@show')->name('wl-payout_report.show');

Route::get('merchant-transaction-excel', 'WLAgent\MerchantTransactionController@exportAllTransaction')->name('wl-merchant-transaction-excel');
Route::get('merchant-crypto-transaction-excel', 'WLAgent\MerchantTransactionController@exportCryptoTransaction')->name('wl-merchant-crypto-transaction-excel');
Route::get('merchant-refund-transaction-excel', 'WLAgent\MerchantTransactionController@exportRefundTransaction')->name('wl-merchant-refund-transaction-excel');
Route::get('merchant-chargebacks-transaction-excel', 'WLAgent\MerchantTransactionController@exportChargebacksTransaction')->name('wl-merchant-chargebacks-transaction-excel');
Route::get('merchant-suspicious-transaction-excel', 'WLAgent\MerchantTransactionController@exportSuspiciousTransaction')->name('wl-merchant-suspicious-transaction-excel');
Route::get('merchant-declined-transaction-excel', 'WLAgent\MerchantTransactionController@exportDeclinedTransaction')->name('wl-merchant-declined-transaction-excel');
Route::get('merchant-retrieval-transaction-excel', 'WLAgent\MerchantTransactionController@exportRetrievalTransaction')->name('wl-merchant-retrieval-transaction-excel');
Route::get('merchant-test-transaction-excel', 'WLAgent\MerchantTransactionController@exportTestTransaction')->name('wl-merchant-test-transaction-excel');

Route::post('merchant-transactions-refund', 'WLAgent\MerchantTransactionController@refund')->name('merchant-transactions-refund');
