<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\FlaggedTransactionMail;
use App\User;
use Mail;

class BulkTransactionSuspiciousQueueEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $UserArr;
    protected $input;
    public $timeout = 7200; // 2 hours

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($UserArr, $input)
    {
        $this->UserArr = $UserArr;
        $this->input = $input;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Mail::to($this->UserArr['user_email'])->send(new FlaggedTransactionMail($this->input));
            $user_additional_mail = getAdditionalFlaggedEmail($this->UserArr['user_id']);
            if($user_additional_mail != null){
                Mail::to($user_additional_mail)->send(new FlaggedTransactionMail($this->input));
            }
        } catch (\Exception $e) {
            \Log::info([
                'error_type' => 'Bulk Transaction Suspicious Queue Email Error',
                'body' => $e->getMessage()
            ]);
        }
    }
}
