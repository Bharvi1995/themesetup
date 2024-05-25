<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Transaction;
use Log;
use Symfony\Component\Process\Process;

class TransactionCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transaction:cron';

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
        $merchant = [18, 65];
        $cardtype = [2, 3];        
        $currency = ['CAD', 'EUR', 'GBP', 'INR', 'USD'];
        $card = ['1741116323579228', '8345601404168690', '1678753898112958', '2535042275890888', '8063352538180734', '3724445707593186', '4601898274966024', '8851476220375863', '1714413261471289', '8668068097669778', '9938839731053172', '1718500145529467'];
        $status = [0, 1, 2, 5];
        $paymentType = [3, 5, 6, 7, 8, 9, 10, 11, 12];

        foreach ($merchant as $user_id) {
            foreach ($currency as $currencyname) {
                foreach ($status as $statusv) {
                    echo "\r\n user_id :: ".$user_id;
                    echo "\r\n currency :: ".$currencyname;
                    echo "\r\n statusv :: ".$statusv;
                    $houseCron = "php ".base_path('artisan') . " transactionall:cron ".$user_id." ".$currencyname." ".$statusv;
                    echo "\r\n ".$houseCron;
                    shell_exec("nohup ".$houseCron." >/dev/null 2>&1 &");
                    // system($houseCron);
                    // $process = new Process([$houseCron]);
                    // $process->setTimeout(0);
                    // $process->disableOutput();
                    // $process->start();
                    // $processes[] = $process;
                    echo "\r\n Cron DONE ==========";
                }
            }
        }
    }
}
