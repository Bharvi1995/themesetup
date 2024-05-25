<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Bank Routes
|--------------------------------------------------------------------------
*/

// Dashboard controller

Route::get('profile', 'Bank\BankUserBaseController@profile')->name('profile-bank');
Route::post('profile-update', 'Bank\BankUserBaseController@updateProfile')->name('bank-profile-update');

// Applications
Route::get('my-application/create', 'Bank\MyApplicationController@create')->name('bank.my-application.create');
Route::get('my-application/edit', 'Bank\MyApplicationController@edit')->name('bank.my-application.edit');
Route::post('my-application/update', 'Bank\MyApplicationController@update')->name('bank.my-application.update');
Route::get('my-application/detail', 'Bank\MyApplicationController@detail')->name('bank.my-application.detail');
Route::post('my-application/store', 'Bank\MyApplicationController@store')->name('bank.my-application.store');

Route::get('downloadDocumentsUploadeBank', 'Bank\ApplicationController@downloadDocumentsUploade')->name('downloadDocumentsUploadeBank');

Route::group(['middleware' => 'bank_application_approved'], function () {
	Route::get('dashboard', 'Bank\BankUserBaseController@dashboard')->name('bank.dashboard');

	Route::get('applications', 'Bank\ApplicationController@list')->name('bank.applications');
	Route::get('approved-applications', 'Bank\ApplicationController@listApproved')->name('bank.applications.approved');
	Route::get('pending-applications', 'Bank\ApplicationController@listPending')->name('bank.applications.pending');
	Route::get('declined-applications', 'Bank\ApplicationController@listDeclined')->name('bank.applications.declined');
	Route::get('referred-applications', 'Bank\ApplicationController@listReferred')->name('bank.applications.referred');
	Route::get('application-pdf/{id}', 'Bank\ApplicationController@downloadPDF')->name('application-pdf-for-bank');
	Route::get('application-docs/{id}', 'Bank\ApplicationController@downloadDOCS')->name('application-docs-for-bank');
	Route::get('application/review/{id}', 'Bank\ApplicationController@applicationReview')->name('bank-application-review');

	Route::post('application-declined', 'Bank\ApplicationController@applicationDeclined')->name('application-declined');
	Route::post('application-referred', 'Bank\ApplicationController@applicationReferred')->name('application-referred');
	Route::post('application-approved', 'Bank\ApplicationController@applicationApproved')->name('application-approved');

	Route::post('applications-list/export', 'Bank\ApplicationController@exportAllApplications')->name('bank.applications.exportAllApplications');
	Route::post('approved-applications-list/export', 'Bank\ApplicationController@exportApprovedApplications')->name('bank.applications.exportApprovedApplications');
	Route::post('declined-applications-list/export', 'Bank\ApplicationController@exportDeclinedApplications')->name('bank.applications.exportDeclinedApplications');
	Route::get('referred-applications-list/export', 'Bank\ApplicationController@exportReferredApplications')->name('bank.applications.exportReferredApplications');
	Route::post('pending-applications-list/export', 'Bank\ApplicationController@exportPendingApplications')->name('bank.applications.exportPendingApplications');

	Route::post('get-application-note-bank-to-admin', 'Bank\ApplicationController@getApplicationNoteBankToAdmin')->name('get-application-note-bank-to-admin');
	Route::post('store-application-note-bank-to-admin', 'Bank\ApplicationController@storeApplicationNoteBankToAdmin')->name('store-application-note-bank-to-admin');

	//Merchant Transactions
	Route::get('merchant-transactions', 'Bank\MerchantTransactionController@index')->name('bank-merchant-transactions');
	Route::post('merchant-transactions-details', 'Bank\MerchantTransactionController@transactionDetails')->name('bank.merchant-transactions-details');
	Route::get('merchant-approved-transactions', 'Bank\MerchantTransactionController@approved')->name('bank-merchant-approved-transactions');
	Route::get('merchant-declined-transactions', 'Bank\MerchantTransactionController@declined')->name('bank-merchant-declined-transactions');
	Route::get('merchant-chargebacks-transactions', 'Bank\MerchantTransactionController@chargebacks')->name('bank-merchant-chargebacks-transactions');
	Route::get('merchant-refund-transactions', 'Bank\MerchantTransactionController@refund')->name('bank-merchant-refund-transactions');

	//Merchant Volume Report
	Route::get('merchant-volume', 'Bank\ReportController@merchantVolumeReport')->name('bank-merchant-volume-report');
	Route::get('merchant-volume-excle', 'Bank\ReportController@merchantVolumeReportExport')->name('bank-merchant-volume-report-excle');
	Route::get('risk-report','Bank\ReportController@riskReport')->name('bank.risk-report');
});
