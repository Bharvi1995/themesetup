<?php

use Illuminate\Database\Seeder;
use App\AdminAction;

class AdminActionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $actions = [
            ['id' => 1, 'title' => 'Login'],
            ['id' => 2, 'title' => 'Logout'],
            ['id' => 3, 'title' => 'Update Profile'],
            ['id' => 4, 'title' => 'Create Admin'],
            ['id' => 5, 'title' => 'Update Admin'],
            ['id' => 6, 'title' => 'Delete Admin'],
            ['id' => 7, 'title' => 'Chanage Admin Status '],
            ['id' => 8, 'title' => 'Expired Admin Password'],
            ['id' => 9, 'title' => 'Create Referral Partner'],
            ['id' => 10, 'title' => 'Update Referral Partner'],
            ['id' => 11, 'title' => 'Delete Referral Partner'],
            ['id' => 12, 'title' => 'Chanage Referral Partner Status '],            
            ['id' => 13, 'title' => 'Referral Partner Document Download'],
            ['id' => 14, 'title' => 'Referral Partner Agreement Status'],
            ['id' => 15, 'title' => 'Generate Payout Report'],
            ['id' => 16, 'title' => 'Payout Report Download Excel'],
            ['id' => 17, 'title' => 'Payout Report Delete'],
            ['id' => 18, 'title' => 'Payout Report Paid'],
            ['id' => 19, 'title' => 'Payout Report Show Client Side'],
            ['id' => 20, 'title' => 'Payout Report Upload Files'],
            ['id' => 21, 'title' => 'Payout Report Generate PDF'],
            ['id' => 22, 'title' => 'Generate Referral Partner Report'],
            ['id' => 23, 'title'  => 'Referral Partner Report Download Excel'],
            ['id' => 24, 'title' => 'Referral Partner Report Delete'],
            ['id' => 25, 'title' => 'Referral Partner Report Paid'],
            ['id' => 26, 'title' => 'Referral Partner Report Show Client Side'],
            ['id' => 27, 'title' => 'Referral Partner Report Upload Files'],
            ['id' => 28, 'title' => 'Referral Partner Report Generate PDF'],
        ];
        foreach($actions as $action) {
            $isexist = AdminAction::find($action['id']);
            if (!$isexist) {
                AdminAction::Create($action);
            }
        }
    }
}
