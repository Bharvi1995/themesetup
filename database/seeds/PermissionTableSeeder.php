<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Delete all records
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Permission::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // add records
        $permissions = [
			'role-list',
			'role-create',
			'role-edit',
			'role-delete',
		];

		foreach ($permissions as $permission) {
			Permission::create([
			    'module' => 'role',
			    'sub_module' => 'role',
			    'name' => $permission,
                'guard_name' => 'admin'
            ]);
		}

        $permissions = [
		    [
		        'module' => 'overview',
                'sub_module' => 'overview',
                'name' => 'overview-transaction-statistics',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'overview',
                'sub_module' => 'overview',
                'name' => 'overview-latest-transactions',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'overview',
                'sub_module' => 'overview',
                'name' => 'overview-latest-tickets',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'overview',
                'sub_module' => 'overview',
                'name' => 'overview-latest-refunds',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'overview',
                'sub_module' => 'overview',
                'name' => 'overview-latest-chargebacks',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'overview',
                'sub_module' => 'overview',
                'name' => 'overview-latest-flagged',
                'guard_name' => 'admin'
            ],

            [
                'module' => 'users',
                'sub_module' => 'user role',
                'name' => 'users-role-view',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'users',
                'sub_module' => 'user role',
                'name' => 'create-role',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'users',
                'sub_module' => 'user role',
                'name' => 'view-role',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'users',
                'sub_module' => 'user role',
                'name' => 'update-role',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'users',
                'sub_module' => 'user role',
                'name' => 'delete-role',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'users',
                'sub_module' => 'user admin',
                'name' => 'users-admin-list',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'users',
                'sub_module' => 'user admin',
                'name' => 'create-admin',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'users',
                'sub_module' => 'user admin',
                'name' => 'update-admin',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'users',
                'sub_module' => 'user admin',
                'name' => 'view-admin',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'users',
                'sub_module' => 'user admin',
                'name' => 'delete-admin',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'users',
                'sub_module' => 'user agent',
                'name' => 'users-agents-list',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'users',
                'sub_module' => 'user agent',
                'name' => 'create-agent',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'users',
                'sub_module' => 'user agent',
                'name' => 'view-agent',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'users',
                'sub_module' => 'user agent',
                'name' => 'update-agent',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'users',
                'sub_module' => 'user agent',
                'name' => 'delete-agent',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'users',
                'sub_module' => 'user agent',
                'name' => 'can-delegate-access-agent',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'users',
                'sub_module' => 'user merchant',
                'name' => 'create-merchant',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'users',
                'sub_module' => 'user merchant',
                'name' => 'view-merchant',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'users',
                'sub_module' => 'user merchant',
                'name' => 'update-merchant',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'users',
                'sub_module' => 'user merchant',
                'name' => 'delete-merchant',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'users',
                'sub_module' => 'user merchant',
                'name' => 'export-merchant',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'users',
                'sub_module' => 'user merchant',
                'name' => 'send-mail-to-merchant',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'users',
                'sub_module' => 'user merchant',
                'name' => 'view-merchant-stores',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'users',
                'sub_module' => 'user merchant',
                'name' => 'sub-users-list',
                'guard_name' => 'admin'
            ],

            [
                'module' => 'users',
                'sub_module' => 'sub user',
                'name' => 'list-sub-user',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'users',
                'sub_module' => 'sub user',
                'name' => 'update-sub-user',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'users',
                'sub_module' => 'sub user',
                'name' => 'delete-sub-user',
                'guard_name' => 'admin'
            ],

        ];
		Permission::insert($permissions);

		$midPermissions = [
		    [
                'module' => 'MID',
                'sub_module' => 'gateway management',
                'name' => 'create-gateway',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'MID',
                'sub_module' => 'gateway management',
                'name' => 'list-gateway',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'MID',
                'sub_module' => 'gateway management',
                'name' => 'update-gateway',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'MID',
                'sub_module' => 'gateway management',
                'name' => 'create-sub-gateway',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'MID',
                'sub_module' => 'gateway management',
                'name' => 'list-sub-gateway',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'MID',
                'sub_module' => 'gateway management',
                'name' => 'update-sub-gateway',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'MID',
                'sub_module' => 'gateway management',
                'name' => 'delete-sub-gateway',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'MID',
                'sub_module' => 'MID',
                'name' => 'list-mid',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'MID',
                'sub_module' => 'MID',
                'name' => 'create-mid',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'MID',
                'sub_module' => 'MID',
                'name' => 'update-mid',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'MID',
                'sub_module' => 'MID',
                'name' => 'delete-mid',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'MID',
                'sub_module' => 'MID',
                'name' => 'view-mid',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'MID',
                'sub_module' => 'rules',
                'name' => 'list-rule',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'MID',
                'sub_module' => 'rules',
                'name' => 'create-rule',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'MID',
                'sub_module' => 'rules',
                'name' => 'update-rule',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'MID',
                'sub_module' => 'rules',
                'name' => 'delete-rule',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'MID',
                'sub_module' => 'rules',
                'name' => 'assign-to-mid',
                'guard_name' => 'admin'
            ],
        ];
        Permission::insert($midPermissions);

        $applicationPermissions = [
            [
                'module' => 'applications',
                'sub_module' => 'applications',
                'name' => 'list-application',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'applications',
                'sub_module' => 'applications',
                'name' => 'update-application',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'applications',
                'sub_module' => 'applications',
                'name' => 'delete-application',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'applications',
                'sub_module' => 'applications',
                'name' => 'view-application',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'applications',
                'sub_module' => 'applications',
                'name' => 'export-application',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'applications',
                'sub_module' => 'applications',
                'name' => 'send-email-application',
                'guard_name' => 'admin'
            ],

            [
                'module' => 'applications',
                'sub_module' => 'application complete',
                'name' => 'list-application-complete',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'applications',
                'sub_module' => 'application complete',
                'name' => 'update-application-complete',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'applications',
                'sub_module' => 'application complete',
                'name' => 'delete-application-complete',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'applications',
                'sub_module' => 'application complete',
                'name' => 'view-application-complete',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'applications',
                'sub_module' => 'application complete',
                'name' => 'export-application-complete',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'applications',
                'sub_module' => 'application complete',
                'name' => 'not-interested-application-complete',
                'guard_name' => 'admin'
            ],

            [
                'module' => 'applications',
                'sub_module' => 'application approved',
                'name' => 'list-application-approved',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'applications',
                'sub_module' => 'application approved',
                'name' => 'update-application-approved',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'applications',
                'sub_module' => 'application approved',
                'name' => 'delete-application-approved',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'applications',
                'sub_module' => 'application approved',
                'name' => 'view-application-approved',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'applications',
                'sub_module' => 'application approved',
                'name' => 'export-application-approved',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'applications',
                'sub_module' => 'application approved',
                'name' => 'not-interested-application-approved',
                'guard_name' => 'admin'
            ],

            [
                'module' => 'applications',
                'sub_module' => 'application rejected',
                'name' => 'list-application-rejected',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'applications',
                'sub_module' => 'application rejected',
                'name' => 'restore-application-rejected',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'applications',
                'sub_module' => 'application rejected',
                'name' => 'delete-application-rejected',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'applications',
                'sub_module' => 'application rejected',
                'name' => 'view-application-rejected',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'applications',
                'sub_module' => 'application rejected',
                'name' => 'export-application-rejected',
                'guard_name' => 'admin'
            ],

            [
                'module' => 'applications',
                'sub_module' => 'application not interested',
                'name' => 'list-application-not-interested',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'applications',
                'sub_module' => 'application not interested',
                'name' => 'update-application-not-interested',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'applications',
                'sub_module' => 'application not interested',
                'name' => 'restore-application-not-interested',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'applications',
                'sub_module' => 'application not interested',
                'name' => 'delete-application-not-interested',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'applications',
                'sub_module' => 'application not interested',
                'name' => 'view-application-not-interested',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'applications',
                'sub_module' => 'application not interested',
                'name' => 'export-application-not-interested',
                'guard_name' => 'admin'
            ],

            [
                'module' => 'applications',
                'sub_module' => 'application terminated',
                'name' => 'list-application-terminated',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'applications',
                'sub_module' => 'application terminated',
                'name' => 'delete-application-terminated',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'applications',
                'sub_module' => 'application terminated',
                'name' => 'view-application-terminated',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'applications',
                'sub_module' => 'application terminated',
                'name' => 'export-application-terminated',
                'guard_name' => 'admin'
            ],
            
            [
                'module' => 'applications',
                'sub_module' => 'application deleted',
                'name' => 'list-application-deleted',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'applications',
                'sub_module' => 'application deleted',
                'name' => 'restore-application-deleted',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'applications',
                'sub_module' => 'application deleted',
                'name' => 'view-application-deleted',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'applications',
                'sub_module' => 'application deleted',
                'name' => 'export-application-deleted',
                'guard_name' => 'admin'
            ],

            [
                'module' => 'applications',
                'sub_module' => 'application agreement send',
                'name' => 'list-application-agreement-send',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'applications',
                'sub_module' => 'application agreement send',
                'name' => 'update-application-agreement-send',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'applications',
                'sub_module' => 'application agreement send',
                'name' => 'delete-application-agreement-send',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'applications',
                'sub_module' => 'application agreement send',
                'name' => 'view-application-agreement-send',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'applications',
                'sub_module' => 'application agreement send',
                'name' => 'export-application-agreement-send',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'applications',
                'sub_module' => 'application agreement send',
                'name' => 'not-interested-application-agreement-send',
                'guard_name' => 'admin'
            ],

            [
                'module' => 'applications',
                'sub_module' => 'application agreement received',
                'name' => 'list-application-agreement-received',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'applications',
                'sub_module' => 'application agreement received',
                'name' => 'update-application-agreement-received',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'applications',
                'sub_module' => 'application agreement received',
                'name' => 'delete-application-agreement-received',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'applications',
                'sub_module' => 'application agreement received',
                'name' => 'view-application-agreement-received',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'applications',
                'sub_module' => 'application agreement received',
                'name' => 'export-application-agreement-received',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'applications',
                'sub_module' => 'application agreement received',
                'name' => 'not-interested-application-agreement-received',
                'guard_name' => 'admin'
            ],
        ];
        Permission::insert($applicationPermissions);

        $transationsPermissions = [
            [
                'module' => 'transactions',
                'sub_module' => 'transactions',
                'name' => 'list-transaction',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'transactions',
                'sub_module' => 'transactions',
                'name' => 'update-transactions',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'transactions',
                'sub_module' => 'transactions',
                'name' => 'delete-transactions',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'transactions',
                'sub_module' => 'transactions',
                'name' => 'view-transactions',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'transactions',
                'sub_module' => 'transactions',
                'name' => 'export-transactions',
                'guard_name' => 'admin'
            ],

            [
                'module' => 'transactions',
                'sub_module' => 'crypto',
                'name' => 'list-crypto-transaction',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'transactions',
                'sub_module' => 'crypto',
                'name' => 'update-crypto-transactions',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'transactions',
                'sub_module' => 'crypto',
                'name' => 'delete-crypto-transactions',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'transactions',
                'sub_module' => 'crypto',
                'name' => 'view-crypto-transactions',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'transactions',
                'sub_module' => 'crypto',
                'name' => 'export-crypto-transactions',
                'guard_name' => 'admin'
            ],

            [
                'module' => 'transactions',
                'sub_module' => 'refund',
                'name' => 'list-refund-transaction',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'transactions',
                'sub_module' => 'refund',
                'name' => 'view-refund-transactions',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'transactions',
                'sub_module' => 'refund',
                'name' => 'export-refund-transactions',
                'guard_name' => 'admin'
            ],

            [
                'module' => 'transactions',
                'sub_module' => 'chargebacks',
                'name' => 'list-chargebacks-transaction',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'transactions',
                'sub_module' => 'chargebacks',
                'name' => 'view-chargebacks-transactions',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'transactions',
                'sub_module' => 'chargebacks',
                'name' => 'export-chargebacks-transactions',
                'guard_name' => 'admin'
            ],

            [
                'module' => 'transactions',
                'sub_module' => 'flagged',
                'name' => 'list-flagged-transaction',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'transactions',
                'sub_module' => 'flagged',
                'name' => 'view-flagged-transactions',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'transactions',
                'sub_module' => 'flagged',
                'name' => 'export-flagged-transactions',
                'guard_name' => 'admin'
            ],

            [
                'module' => 'transactions',
                'sub_module' => 'test',
                'name' => 'list-test-transaction',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'transactions',
                'sub_module' => 'test',
                'name' => 'view-test-transactions',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'transactions',
                'sub_module' => 'test',
                'name' => 'export-test-transactions',
                'guard_name' => 'admin'
            ],

            [
                'module' => 'transactions',
                'sub_module' => 'remove flagged',
                'name' => 'list-remove-flagged-transaction',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'transactions',
                'sub_module' => 'remove flagged',
                'name' => 'view-remove-flagged-transactions',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'transactions',
                'sub_module' => 'remove flagged',
                'name' => 'export-remove-flagged-transactions',
                'guard_name' => 'admin'
            ],
        ];

        Permission::insert($transationsPermissions);

        $reportPermissions = [
            [
                'module' => 'reports',
                'sub_module' => 'transaction summary',
                'name' => 'view-transaction-summary',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'reports',
                'sub_module' => 'transaction summary',
                'name' => 'export-transaction-summary',
                'guard_name' => 'admin'
            ]
        ];
        Permission::insert($reportPermissions);

        $technicalPermissions = [
            [
                'module' => 'technical',
                'sub_module' => 'ip whitelist',
                'name' => 'list-ip-whitelist',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'technical',
                'sub_module' => 'ip whitelist',
                'name' => 'refuse-ip-whitelist',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'technical',
                'sub_module' => 'iframe generator',
                'name' => 'list-ip-iframe-generator',
                'guard_name' => 'admin'
            ],
        ];
        Permission::insert($technicalPermissions);

        $supportPermissions = [
            [
                'module' => 'support',
                'sub_module' => 'category',
                'name' => 'list-category',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'support',
                'sub_module' => 'category',
                'name' => 'create-category',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'support',
                'sub_module' => 'category',
                'name' => 'update-category',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'support',
                'sub_module' => 'category',
                'name' => 'delete-category',
                'guard_name' => 'admin'
            ],

            [
                'module' => 'support',
                'sub_module' => 'technology partner',
                'name' => 'list-technology-partner',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'support',
                'sub_module' => 'technology partner',
                'name' => 'create-technology-partner',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'support',
                'sub_module' => 'technology partner',
                'name' => 'update-technology-partner',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'support',
                'sub_module' => 'technology partner',
                'name' => 'delete-technology-partner',
                'guard_name' => 'admin'
            ],

            [
                'module' => 'support',
                'sub_module' => 'ticket',
                'name' => 'list-ticket',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'support',
                'sub_module' => 'ticket',
                'name' => 'delete-ticket',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'support',
                'sub_module' => 'ticket',
                'name' => 'view-ticket',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'support',
                'sub_module' => 'ticket',
                'name' => 'close-ticket',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'support',
                'sub_module' => 'ticket',
                'name' => 'reply-ticket',
                'guard_name' => 'admin'
            ],
        ];
        Permission::insert($supportPermissions);

        $tutorialPermissions = [
            [
                'module' => 'tutorial',
                'sub_module' => 'article',
                'name' => 'list-article',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'tutorial',
                'sub_module' => 'article',
                'name' => 'create-article',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'tutorial',
                'sub_module' => 'article',
                'name' => 'update-article',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'tutorial',
                'sub_module' => 'article',
                'name' => 'view-article',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'tutorial',
                'sub_module' => 'article',
                'name' => 'delete-article',
                'guard_name' => 'admin'
            ],

            [
                'module' => 'tutorial',
                'sub_module' => 'article category',
                'name' => 'list-article-category',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'tutorial',
                'sub_module' => 'article category',
                'name' => 'create-article-category',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'tutorial',
                'sub_module' => 'article category',
                'name' => 'update-article-category',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'tutorial',
                'sub_module' => 'article category',
                'name' => 'delete-article-category',
                'guard_name' => 'admin'
            ],

            [
                'module' => 'tutorial',
                'sub_module' => 'article tag',
                'name' => 'list-article-tag',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'tutorial',
                'sub_module' => 'article tag',
                'name' => 'create-article-tag',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'tutorial',
                'sub_module' => 'article tag',
                'name' => 'update-article-tag',
                'guard_name' => 'admin'
            ],
            [
                'module' => 'tutorial',
                'sub_module' => 'article tag',
                'name' => 'delete-article-tag',
                'guard_name' => 'admin'
            ],
        ];
        Permission::insert($tutorialPermissions);

    }
}
