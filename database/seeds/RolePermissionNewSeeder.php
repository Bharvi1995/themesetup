<?php

use Illuminate\Database\Seeder;
use App\Permission;

class RolePermissionNewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $actions = [
        	[
        		'module' => 'overview',
        		'sub_module' => 'overview',
        		'name' => 'overview-view',
        		'guard_name' => 'admin'
        	],[
        		'module' => 'overview',
        		'sub_module' => 'overview',
        		'name' => 'overview-transaction-statistics',
        		'guard_name' => 'admin'
        	],[
        		'module' => 'overview',
        		'sub_module' => 'overview',
        		'name' => 'overview-latest-transactions',
        		'guard_name' => 'admin'
        	],[
        		'module' => 'overview',
        		'sub_module' => 'overview',
        		'name' => 'overview-highest-processing-merchants',
        		'guard_name' => 'admin'
        	],[
        		'module' => 'overview',
        		'sub_module' => 'overview',
        		'name' => 'overview-highest-processing-mid',
        		'guard_name' => 'admin'
        	],[
        		'module' => 'overview',
        		'sub_module' => 'overview',
        		'name' => 'overview-merchant-chargeback-frequency',
        		'guard_name' => 'admin'
        	],[
        		'module' => 'overview',
        		'sub_module' => 'overview',
        		'name' => 'overview-merchant-suspicious-frequency',
        		'guard_name' => 'admin'
        	],[
        		'module' => 'overview',
        		'sub_module' => 'overview',
        		'name' => 'overview-merchant-refund-frequency',
        		'guard_name' => 'admin'
        	],

        	[
        		'module' => 'role',
        		'sub_module' => 'role',
        		'name' => 'role-list',
        		'guard_name' => 'admin'
        	],[
	            'module' => 'role',
	            'sub_module' => 'role',
	            'name' => 'role-create',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'role',
	            'sub_module' => 'role',
	            'name' => 'role-edit',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'role',
	            'sub_module' => 'role',
	            'name' => 'role-delete',
	            'guard_name' => 'admin'
            ],

            [
	            'module' => 'users',
	            'sub_module' => 'user admin',
	            'name' => 'users-admin-list',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'users',
	            'sub_module' => 'user admin',
	            'name' => 'users-admin-excel-export',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'users',
	            'sub_module' => 'user admin',
	            'name' => 'delete-admin',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'users',
	            'sub_module' => 'user admin',
	            'name' => 'create-admin',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'users',
	            'sub_module' => 'user admin',
	            'name' => 'update-admin',
	            'guard_name' => 'admin'
            ],

            [
	            'module' => 'users',
	            'sub_module' => 'user bank',
	            'name' => 'users-bank-list',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'users',
	            'sub_module' => 'user bank',
	            'name' => 'users-bank-excel-export',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'users',
	            'sub_module' => 'user bank',
	            'name' => 'delete-users-bank',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'users',
	            'sub_module' => 'user bank',
	            'name' => 'users-bank-send-mail',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'users',
	            'sub_module' => 'user bank',
	            'name' => 'users-bank-create',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'users',
	            'sub_module' => 'user bank',
	            'name' => 'users-bank-update',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'users',
	            'sub_module' => 'user bank',
	            'name' => 'can-delegate-access-bank',
	            'guard_name' => 'admin'
            ],

            [
	            'module' => 'users',
	            'sub_module' => 'user agent',
	            'name' => 'users-agents-list',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'users',
	            'sub_module' => 'user agent',
	            'name' => 'users-agents-excel-export',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'users',
	            'sub_module' => 'user agent',
	            'name' => 'delete-agent',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'users',
	            'sub_module' => 'user agent',
	            'name' => 'users-agents-send-mail',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'users',
	            'sub_module' => 'user agent',
	            'name' => 'create-agent',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'users',
	            'sub_module' => 'user agent',
	            'name' => 'update-agent',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'users',
	            'sub_module' => 'user agent',
	            'name' => 'can-delegate-access-agent',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'users',
	            'sub_module' => 'user agent',
	            'name' => 'agent-bank-detail-view',
	            'guard_name' => 'admin'
            ],

            [
	            'module' => 'users',
	            'sub_module' => 'user merchant',
	            'name' => 'view-merchant',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'users',
	            'sub_module' => 'user merchant',
	            'name' => 'export-merchant',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'users',
	            'sub_module' => 'user merchant',
	            'name' => 'send-mail-to-merchant',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'users',
	            'sub_module' => 'user merchant',
	            'name' => 'delete-merchant',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'users',
	            'sub_module' => 'user merchant',
	            'name' => 'create-merchant',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'users',
	            'sub_module' => 'user merchant',
	            'name' => 'update-merchant',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'users',
	            'sub_module' => 'user merchant',
	            'name' => 'sub-users-list',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'users',
	            'sub_module' => 'user merchant',
	            'name' => 'assign-reffrel-partner',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'users',
	            'sub_module' => 'user merchant',
	            'name' => 'merchant-assign-mid',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'users',
	            'sub_module' => 'user merchant',
	            'name' => 'merchant-view-bank-details',
	            'guard_name' => 'admin'
            ],

            [
	            'module' => 'users',
	            'sub_module' => 'user wl agent',
	            'name' => 'view-wl-rp',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'users',
	            'sub_module' => 'user wl agent',
	            'name' => 'wl-rp-merchant-list',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'users',
	            'sub_module' => 'user wl agent',
	            'name' => 'export-wl-rp',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'users',
	            'sub_module' => 'user wl agent',
	            'name' => 'delete-wl-rp',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'users',
	            'sub_module' => 'user wl agent',
	            'name' => 'update-wl-rp',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'users',
	            'sub_module' => 'user wl agent',
	            'name' => 'create-wl-rp',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'users',
	            'sub_module' => 'user wl agent',
	            'name' => 'wl-rp-merchant-management',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'users',
	            'sub_module' => 'user wl agent',
	            'name' => 'export-wl-rp-merchant',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'users',
	            'sub_module' => 'user wl agent',
	            'name' => 'delete-wl-rp-merchant',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'users',
	            'sub_module' => 'user wl agent',
	            'name' => 'update-wl-rp-merchant',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'users',
	            'sub_module' => 'user wl agent',
	            'name' => 'view-wl-rp-merchant',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'users',
	            'sub_module' => 'user wl agent',
	            'name' => 'wl-rp-merchant-create',
	            'guard_name' => 'admin'
            ],

            [
	            'module' => 'users',
	            'sub_module' => 'user merchant store',
	            'name' => 'view-merchant-stores',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'users',
	            'sub_module' => 'user merchant store',
	            'name' => 'merchant-store-excel-export',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'users',
	            'sub_module' => 'user merchant store',
	            'name' => 'merchant-store-products',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'users',
	            'sub_module' => 'user merchant store',
	            'name' => 'merchant-store-products-update',
	            'guard_name' => 'admin'
            ],

            [
	            'module' => 'mid',
	            'sub_module' => 'gateway management',
	            'name' => 'list-gateway',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'mid',
	            'sub_module' => 'gateway management',
	            'name' => 'create-gateway',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'mid',
	            'sub_module' => 'gateway management',
	            'name' => 'update-gateway',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'mid',
	            'sub_module' => 'gateway management',
	            'name' => 'list-sub-gateway',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'mid',
	            'sub_module' => 'gateway management',
	            'name' => 'create-sub-gateway',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'mid',
	            'sub_module' => 'gateway management',
	            'name' => 'update-sub-gateway',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'mid',
	            'sub_module' => 'gateway management',
	            'name' => 'delete-sub-gateway',
	            'guard_name' => 'admin'
            ],

            [
	            'module' => 'mid',
	            'sub_module' => 'mid',
	            'name' => 'list-mid',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'mid',
	            'sub_module' => 'mid',
	            'name' => 'create-mid',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'mid',
	            'sub_module' => 'mid',
	            'name' => 'update-mid',
	            'guard_name' => 'admin'
            ],

            [
	            'module' => 'mid',
	            'sub_module' => 'rules',
	            'name' => 'list-rule',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'mid',
	            'sub_module' => 'rules',
	            'name' => 'create-rule',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'mid',
	            'sub_module' => 'rules',
	            'name' => 'delete-rule',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'mid',
	            'sub_module' => 'rules',
	            'name' => 'update-rule',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'mid',
	            'sub_module' => 'rules',
	            'name' => 'assign-to-mid-rule',
	            'guard_name' => 'admin'
            ],

            [
	            'module' => 'mid',
	            'sub_module' => 'merchant rules',
	            'name' => 'merchant-rule-list',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'mid',
	            'sub_module' => 'merchant rules',
	            'name' => 'merchant-rule-update',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'mid',
	            'sub_module' => 'merchant rules',
	            'name' => 'merchant-rule-delete',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'mid',
	            'sub_module' => 'merchant rules',
	            'name' => 'merchant-rule-assign-to-mid',
	            'guard_name' => 'admin'
            ],

            [
	            'module' => 'applications',
	            'sub_module' => 'merchant applications',
	            'name' => 'list-application',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'applications',
	            'sub_module' => 'merchant applications',
	            'name' => 'export-application',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'applications',
	            'sub_module' => 'merchant applications',
	            'name' => 'delete-application',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'applications',
	            'sub_module' => 'merchant applications',
	            'name' => 'send-email-application',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'applications',
	            'sub_module' => 'merchant applications',
	            'name' => 'pdf-download-application',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'applications',
	            'sub_module' => 'merchant applications',
	            'name' => 'doc-download-application',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'applications',
	            'sub_module' => 'merchant applications',
	            'name' => 'view-application',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'applications',
	            'sub_module' => 'merchant applications',
	            'name' => 'update-application',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'applications',
	            'sub_module' => 'merchant applications',
	            'name' => 'delete-application',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'applications',
	            'sub_module' => 'merchant applications',
	            'name' => 'send-to-bank-application',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'applications',
	            'sub_module' => 'merchant applications',
	            'name' => 'not-interested-application',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'applications',
	            'sub_module' => 'merchant applications',
	            'name' => 'agreement-action-application',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'applications',
	            'sub_module' => 'merchant applications',
	            'name' => 'current-status-note-application',
	            'guard_name' => 'admin'
            ],

            [
	            'module' => 'applications',
	            'sub_module' => 'bank applications',
	            'name' => 'view-bank-application',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'applications',
	            'sub_module' => 'bank applications',
	            'name' => 'delete-bank-application',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'applications',
	            'sub_module' => 'bank applications',
	            'name' => 'update-bank-application',
	            'guard_name' => 'admin'
            ],

            [
	            'module' => 'applications',
	            'sub_module' => 'agent applications',
	            'name' => 'view-rp-application',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'applications',
	            'sub_module' => 'agent applications',
	            'name' => 'delete-rp-application',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'applications',
	            'sub_module' => 'agent applications',
	            'name' => 'update-rp-application',
	            'guard_name' => 'admin'
            ],

            [
	            'module' => 'transactions',
	            'sub_module' => 'all transactions',
	            'name' => 'list-all-transaction',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'transactions',
	            'sub_module' => 'all transactions',
	            'name' => 'details-transaction',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'transactions',
	            'sub_module' => 'all transactions',
	            'name' => 'company-name',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'transactions',
	            'sub_module' => 'all transactions',
	            'name' => 'export-all-transaction',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'transactions',
	            'sub_module' => 'all transactions',
	            'name' => 'delete-all-transaction',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'transactions',
	            'sub_module' => 'all transactions',
	            'name' => 'update-all-transaction',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'transactions',
	            'sub_module' => 'all transactions',
	            'name' => 'send-webhook-all-transaction',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'transactions',
	            'sub_module' => 'refund transactions',
	            'name' => 'update-refund-transaction',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'transactions',
	            'sub_module' => 'chargeback transactions',
	            'name' => 'pre-arbitration-notice-chargeback-transaction',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'transactions',
	            'sub_module' => 'chargeback transactions',
	            'name' => 'send-mail-chargeback-transaction',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'transactions',
	            'sub_module' => 'retrival transactions',
	            'name' => 'send-mail-retrival-transaction',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'transactions',
	            'sub_module' => 'refund transactions',
	            'name' => 'send-mail-refund-transaction',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'transactions',
	            'sub_module' => 'retrival transactions',
	            'name' => 'update-retrival-transaction',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'transactions',
	            'sub_module' => 'suspicious transactions',
	            'name' => 'update-suspicious-transaction',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'transactions',
	            'sub_module' => 'suspicious transactions',
	            'name' => 'send-mail-suspicious-transaction',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'transactions',
	            'sub_module' => 'pre arbitration transactions',
	            'name' => 'send-pre-arbitration-notice',
	            'guard_name' => 'admin'
            ],

            [
	            'module' => 'report',
	            'sub_module' => 'transaction summary report',
	            'name' => 'list-transaction-summary-report',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'report',
	            'sub_module' => 'transaction summary report',
	            'name' => 'export-transaction-summary-report',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'report',
	            'sub_module' => 'transaction merchant report',
	            'name' => 'list-merchant-transaction-report',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'report',
	            'sub_module' => 'transaction merchant report',
	            'name' => 'export-merchant-transaction-report',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'report',
	            'sub_module' => 'card type report',
	            'name' => 'list-card-type-report',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'report',
	            'sub_module' => 'card type report',
	            'name' => 'export-card-type-report',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'report',
	            'sub_module' => 'payment status summary report',
	            'name' => 'export-payment-status-summary-report',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'report',
	            'sub_module' => 'mid summary report',
	            'name' => 'list-mid-summary-report',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'report',
	            'sub_module' => 'mid summary report',
	            'name' => 'export-mid-summary-report',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'report',
	            'sub_module' => 'country summary report',
	            'name' => 'list-country-summary-report',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'report',
	            'sub_module' => 'country summary report',
	            'name' => 'export-country-summary-report',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'report',
	            'sub_module' => 'auto suspicious report',
	            'name' => 'export-auto-suspicious-report',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'report',
	            'sub_module' => 'auto suspicious report',
	            'name' => 'make-auto-suspicious',
	            'guard_name' => 'admin'
            ],

            [
	            'module' => 'admin report',
	            'sub_module' => 'merchant transaction responses',
	            'name' => 'export-merchant-transaction-responses',
	            'guard_name' => 'admin'
            ],

            [
	            'module' => 'payout report',
	            'sub_module' => 'generate merchant payout report',
	            'name' => 'form-generate-payout-reports',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'payout report',
	            'sub_module' => 'generate merchant payout report',
	            'name' => 'export-generated-payout-reports',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'payout report',
	            'sub_module' => 'generate merchant payout report',
	            'name' => 'update-generated-payout-reports',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'payout report',
	            'sub_module' => 'generate merchant payout report',
	            'name' => 'show-generated-payout-reports',
	            'guard_name' => 'admin'
            ],

            [
	            'module' => 'payout report',
	            'sub_module' => 'generate agent payout report',
	            'name' => 'list-generated-rp-payout-reports',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'payout report',
	            'sub_module' => 'generate agent payout report',
	            'name' => 'form-generate-rp-payout-reports',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'payout report',
	            'sub_module' => 'generate agent payout report',
	            'name' => 'export-generated-rp-payout-reports',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'payout report',
	            'sub_module' => 'generate agent payout report',
	            'name' => 'update-generated-rp-payout-reports',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'payout report',
	            'sub_module' => 'generate agent payout report',
	            'name' => 'delete-generated-rp-payout-reports',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'payout report',
	            'sub_module' => 'generate agent payout report',
	            'name' => 'show-generated-rp-payout-reports',
	            'guard_name' => 'admin'
            ],

            [
	            'module' => 'payout report',
	            'sub_module' => 'generate wl agent payout report',
	            'name' => 'list-generated-wl-rp-payout-reports',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'payout report',
	            'sub_module' => 'generate wl agent payout report',
	            'name' => 'form-generate-wl-rp-payout-reports',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'payout report',
	            'sub_module' => 'generate wl agent payout report',
	            'name' => 'export-generated-wl-rp-payout-reports',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'payout report',
	            'sub_module' => 'generate wl agent payout report',
	            'name' => 'update-generated-wl-rp-payout-reports',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'payout report',
	            'sub_module' => 'generate wl agent payout report',
	            'name' => 'delete-generated-wl-rp-payout-reports',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'payout report',
	            'sub_module' => 'generate wl agent payout report',
	            'name' => 'show-generated-wl-rp-payout-reports',
	            'guard_name' => 'admin'
            ],

            [
	            'module' => 'report',
	            'sub_module' => 'blocked system',
	            'name' => 'view-blocked-system',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'report',
	            'sub_module' => 'blocked system',
	            'name' => 'delete-block-card',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'report',
	            'sub_module' => 'cron management',
	            'name' => 'view-cron-management',
	            'guard_name' => 'admin'
            ],

            [
	            'module' => 'technical additional',
	            'sub_module' => 'ip whitelist',
	            'name' => 'view-ip-whitelist',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'technical additional',
	            'sub_module' => 'iframe generator',
	            'name' => 'view-iframe-generator',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'technical additional',
	            'sub_module' => 'transaction session data',
	            'name' => 'view-transaction-session-data',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'technical additional',
	            'sub_module' => 'required fields',
	            'name' => 'view-required-fields',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'technical additional',
	            'sub_module' => 'email card block system',
	            'name' => 'view-email-card-block-system',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'technical additional',
	            'sub_module' => 'industry type',
	            'name' => 'view-industry-type',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'technical additional',
	            'sub_module' => 'integration preference',
	            'name' => 'view-integration-preference',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'technical additional',
	            'sub_module' => 'admin logs',
	            'name' => 'view-admin-logs',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'technical additional',
	            'sub_module' => 'mail templates',
	            'name' => 'view-mail-templates',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'technical additional',
	            'sub_module' => 'mass mid switching',
	            'name' => 'view-mass-mid-switching',
	            'guard_name' => 'admin'
            ],

            [
	            'module' => 'extra',
	            'sub_module' => 'tickets',
	            'name' => 'list-ticket',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'extra',
	            'sub_module' => 'tickets',
	            'name' => 'show-ticket',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'extra',
	            'sub_module' => 'tickets',
	            'name' => 'delete-ticket',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'extra',
	            'sub_module' => 'tickets',
	            'name' => 'update-ticket',
	            'guard_name' => 'admin'
            ],

            [
	            'module' => 'extra',
	            'sub_module' => 'agreement upload',
	            'name' => 'view-agreement-upload',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'extra',
	            'sub_module' => 'agreement upload',
	            'name' => 'merchant-agreement-upload',
	            'guard_name' => 'admin'
            ],[
	            'module' => 'extra',
	            'sub_module' => 'agreement upload',
	            'name' => 'rp-agreement-upload',
	            'guard_name' => 'admin'
            ],
        ];

        foreach($actions as $action) {
        	$isexist = Permission::where('name',$action['name'])->first();
            if (!$isexist) {
                Permission::Create($action);
            }
        }
    }
}
