<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Transaction;
use App\MIDDetail;
use Mail;

class SendBulkWebhookInQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $details;
    public $timeout = 7200; // 2 hours

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->details = $details;
        $this->MIDDetail = new MIDDetail;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $ids = $this->details['ids'];

        foreach ($ids as $key => $value) {
            // \Log::info('webhook Queue working');
            $transaction = Transaction::where('id', $value)->first();
            if(isset($transaction->webhook_url) && $transaction->webhook_url != null) {
                $paymentGatewayId = MIDDetail::where('id',$transaction->payment_gateway_id)->first();
                if ($transaction->status == '1') {
                    $transactionStatus = 'success';
                } elseif ($transaction->status == '2') {
                    $transactionStatus = 'pending';
                } elseif ($transaction->status == '5') {
                    $transactionStatus = 'blocked';
                } else {
                    $transactionStatus = 'fail';
                }
                $request_data['order_id'] = $transaction->order_id;
                $request_data['customer_order_id'] = $transaction->customer_order_id ?? null;
                $request_data['transaction_status'] = $transactionStatus;
                $request_data['reason'] = $transaction->reason;
                $request_data['currency'] = $transaction->currency;
                $request_data['amount'] = $transaction->amount;
                $request_data['transaction_date'] = $transaction->created_at;
                $request_data["descriptor"] = $paymentGatewayId->descriptor;
                // send webhook request
                try {
                    $http_response = postCurlRequest($transaction->webhook_url, $request_data);
                } catch (Exception $e) {
                    $http_response = 'FAILED';
                }
                $input['webhook_status'] = $http_response;
                $input['webhook_retry'] = 1;
            }
        }
    }
}
