<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Transaction;
use Log;

class TransactionCronWithArg extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transactionall:cron {id?} {currency?} {status?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload 2 lacs random record in table';

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
     * @return mixed
     */
    public function handle()
    {
        $user_id = $this->argument('id');
        $currencyname = $this->argument('currency');
        $statusv = $this->argument('status');
        
        $cardtype = [2, 3];
        $card = ['1741116323579228', '8345601404168690', '1678753898112958', '2535042275890888', '8063352538180734', '3724445707593186', '4601898274966024', '8851476220375863', '1714413261471289', '8668068097669778', '9938839731053172', '1718500145529467'];
        $status = [0, 1, 2, 5];
        $paymentType = [3, 5, 6, 7, 8, 9, 10, 11, 12];

        echo "\r\n user_id :: ".$user_id;
        echo "\r\n currency :: ".$currencyname;
        echo "\r\n statusv :: ".$statusv;
        $num = 10000;
        $arrRc = [12000, 13000, 14000, 15000,16000, 17000];
        shuffle($arrRc);
        $num = $arrRc[0];
        echo "\r\n num :: ".$num;
        // $num = 5000;
        $_allinput = array();
        for ($i=0; $i < $num; $i++) {
            $transaction_date = date("Y-m-d H:i:s", strtotime('-'.$i.' day'));
            //echo "\r\n transaction_date :: ".$transaction_date;
            $userkey = rand(0,1);
            $cardkey = rand(0,11);
            $paymentKey = rand(0,8);
            $reason = '';
            if($statusv == 1){
                $reason = "Approved";
            } else if($statusv == 2){
                $reason = "Your transaction is pending confirmation from the bank . It will be confirmed within next 5 minutes and you will be able to check the updated status in your dashboard.";
            } else if($statusv == 5){
                $reason = "Your transaction was declined.";
            }
            $input = array();
            $input['user_id']           = $user_id;
            $input['currency']          = $currencyname;
            $input['transaction_date']  = $transaction_date;

            $input['order_id'] = time().strtoupper(\Str::random(10));
            $input['session_id'] = strtoupper(\Str::random(4)).time();
            $input['first_name'] = strtoupper(\Str::random(10));
            $input['last_name'] = strtoupper(\Str::random(10));
            $input['address'] = strtoupper(\Str::random(20));
            $input['customer_order_id'] = '';
            $input['country'] = 'IN';
            $input['state'] = strtoupper(\Str::random(10));
            $input['city'] = strtoupper(\Str::random(10));
            $input['zip'] = 380015;
            $input['ip_address'] = '';
            $input['email'] = strtoupper(\Str::random(10)) . "@gmail.com";
            $input['country_code'] = '';
            $input['phone_no'] = rand(1111111111,9999999999);
            $input['card_type'] = $cardtype[$userkey];
            $input['amount'] = rand(999, 9999) / 10;;
            $input['amount_in_usd'] = '0.00';
            
            $input['card_no'] = $card[$cardkey];
            $input['ccExpiryMonth'] = '12';
            $input['ccExpiryYear'] = '2022';
            $input['status'] = $statusv;
            $input['reason'] = $reason;
            $input['descriptor'] = '';        
            $input['payment_gateway_id'] = $paymentType[$paymentKey];
            $input['payment_type'] = '';
            $input['merchant_discount_rate'] = '0.00';
            $input['bank_discount_rate'] = '0.00';
            $input['net_profit_amount'] = '0.00';
            $input['created_at'] = $transaction_date;
            $input['updated_at'] = $transaction_date;
            $_allinput[] = $input;
        }
        echo " \r\n ". date("Y-m-d H:i:s"). " Start ====> COUNT ===> ".count($_allinput) . " \r\n";
        $chunk_data = array_chunk($_allinput, 1000);
        foreach ($chunk_data as $d1){
            try {
                Transaction::insert($d1);
            }catch (\Exception $e) {
                echo "\r\n insert getMessage :: ";
                echo $e->getMessage();
                echo "\r\n";
                continue;
            }
        }

    }
}
