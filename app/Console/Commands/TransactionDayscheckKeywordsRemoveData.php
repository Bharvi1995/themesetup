<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\CronManagements;
use App\Transaction;

class TransactionDayscheckKeywordsRemoveData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transaction:data_mining';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
     echo "\r\n Start :: ".date('Y-m-d H:i:s');
     $CronManagements = CronManagements::where('id',1)->where('status', 1)->first();
     if($CronManagements) {

        CronManagements::where('id', $CronManagements->id)->update(['current_status' => 1]);
        echo "\r\n Day From :: ".$CronManagements["days_check"];
        $CronManagements = $CronManagements->toArray();
        $keywords = json_decode($CronManagements['keywords']);
        if(!empty($keywords)) {
            // \DB::enableQueryLog();
            $today = date('Y-m-d'); 
            $daysbefore = date('Y-m-d', strtotime('-'.$CronManagements["days_check"].' days'));
            $transaction_data = Transaction::select('user_id','order_id','country','card_type','amount','amount_in_usd','currency','card_no','status','reason','created_at', \DB::RAW('DATE(NOW()) AS updated_at'))
                                            ->Where(function ($query) use($keywords) {
                                                    for ($i = 0; $i < count($keywords); $i++){
                                                        $query->orwhere('reason', 'like',  '%' . $keywords[$i] .'%');
                                                    }
                                                })
                                            ->where('created_at','>=',$daysbefore.' 00:00:00')
                                            ->where('created_at','<=',$today.' 23:59:59')
                                            ->where('card_no',"!=",'')
                                            ->groupBy('card_no','reason')
                                            ->get()->toArray();
            // dd(\DB::getQueryLog());
            if($transaction_data) {
               \DB::table('block_cards')->truncate();
               $chunk_data = array_chunk($transaction_data, 1000);
               foreach ($chunk_data as $d1){
                    try {
                     \DB::table('block_cards')->insert($d1);
                     }catch (\Exception $e) {
                        echo "\r\n insert getMessage :: ";
                        echo $e->getMessage();
                        echo "\r\n";
                        continue;
                    }
                }
            }
        }
     }

     CronManagements::where('id', $CronManagements['id'])->update(['current_status' => 0, 'last_run_at'=>NOW()]);
     echo "\r\n End :: ".date('Y-m-d H:i:s');
    }
}
