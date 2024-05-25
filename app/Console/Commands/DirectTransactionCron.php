<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Transaction;
use Log;
use Symfony\Component\Process\Process;
use URL;

class DirectTransactionCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'directtransaction:cron {count?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload random record in table';

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
        $count = ($this->argument('count') > 0) ? $this->argument('count') : 10;
        for($i = 0; $i < $count; $i ++){
            $houseCron = "php ".base_path('artisan') . " inserttransaction:cron";
            echo "\r\n ".$houseCron;
            shell_exec("nohup ".$houseCron." >/dev/null 2>&1 &");
        }
        echo "\r\n Cron DONE ==========";
    }
}
