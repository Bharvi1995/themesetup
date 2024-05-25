<?php
return [

	'default_email' => 'sales@testpay.com',

	'default_admin_id' => 27,

	'notification_type' => [
		// notification generated by admin for user
		'transaction_flag',
		'transaction_retrieval',
		'transaction_decline',
		'transaction_chargeback',
		'transaction_refund',
		'ip_approve',

		// notification generated by merchant for admin 
		'ip_approve_request',
		'ticket_generated',
		'refund_request',
		'email_send',
	],

	// notification generated by admin for user
	'transaction_flag' => [
		'url' => '/suspicious'
	],

	'transaction_retrieval' => [
		'url' => '/retrieval'
	],

	'transaction_decline' => [
		'url' => '/transactions'
	],

	'transaction_chargeback' => [
		'url' => '/chargebacks'
	],

	'transaction_refund' => [
		'url' => '/refund'
	],

	'ip_approve' => [
		'url' => '/user-api-key'
	],

	// notification generated by merchant for admin 
	'ip_approve_request' => [
		'url' => '/admin/api-key'
	],

	'ticket_generated' => [
		'url' => '/admin/ticket'
	],

	'email_send' => [
		'url' => '/admin/email'
	],

	'refund_request' => [
		'url' => '/admin/merchant-refund'
	],

	'flagged_document_upload' => [
		'url' => '/admin/merchant-flagged'
	],

	'chargeback_document_upload' => [
		'url' => '/admin/merchant-chargebacks'
	],

	'retrieval_document_upload' => [
		'url' => '/admin/merchant-retrieval'
	],
];