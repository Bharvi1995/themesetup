<?php

use Illuminate\Database\Seeder;
use App\CronManagements;

class CronManagementTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       $actions = [
       	['id'=>1, 'cron_name'=>'Data Mining ', 'command'=>'transaction:data_mining', 'description'=>'Trasaction Data Mining For Block the cards', 'status'=>0, 'current_status'=>0, 'keywords'=>'', 'days_check'=>7, 'created_at'=>NOW(), 'updated_at'=>NOW(), 'deleted_at'=>NULL],  
       ];
        foreach($actions as $action) {
            $isexist = CronManagements::find($action['id']);
            if (!$isexist) {
                CronManagements::Create($action);
            }
        }
    }
}
