<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Transaction;
use Log;
use URL;

class InsertTransactionCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inserttransaction:cron';

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
        $num = 100;
        $arrRc = [200, 300, 400, 500, 600, 700];
        /*shuffle($arrRc);
        $num = $arrRc[0];*/
        for ($i=0; $i < $num; $i++) {            

            $time_start = microtime(true);
            $order_id = time().strtoupper(\Str::random(10));
            $url = URL::to('/');

            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => $url . "/api/transaction",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS => "------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"api_key\"\r\n\r\n14|dvSxXxg56Rkk2rCjUpzKzXHPSBcj7rpH3RebWM3X\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"first_name\"\r\n\r\nTest\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"last_name\"\r\n\r\nTest\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"address\"\r\n\r\nAhmedabad\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"customer_order_id\"\r\n\r\n$order_id\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"country\"\r\n\r\nUS\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"state\"\r\n\r\nNY\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"city\"\r\n\r\nNew York\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"zip\"\r\n\r\n38564\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"ip_address\"\r\n\r\n192.168.168.4\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"email\"\r\n\r\ntest@gmail.com\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"country_code\"\r\n\r\n+91\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"phone_no\"\r\n\r\n999999999\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"amount\"\r\n\r\n15\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"currency\"\r\n\r\nUSD\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"card_no\"\r\n\r\n4242424242424242\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"ccExpiryMonth\"\r\n\r\n02\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"ccExpiryYear\"\r\n\r\n2026\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"cvvNumber\"\r\n\r\n123\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"response_url\"\r\n\r\nhttps://testpay.com\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW--",
              CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW",
                "postman-token: 93262713-24a6-81f8-17a3-3da4be3c3007"
              ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);
            $time_end = microtime(true);
            
            if ($err) {
              echo "cURL Error #:" . $err;
            } else {

                $execution_time = ($time_end - $time_start)/60;
                //execution time of the script
                echo "\r\n <b>Total Execution Time:</b> ".$execution_time . " min";
              //echo "\r\n".$response;
            }

        }
    }
}
