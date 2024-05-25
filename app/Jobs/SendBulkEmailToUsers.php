<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\SendUserMultiMail;
use Illuminate\Support\Facades\Mail;

class SendBulkEmailToUsers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $emails, $input;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($emails, $input)
    {
        $this->emails = $emails;
        $this->input = $input;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->emails as $email) {
            Mail::to($email)->send(new SendUserMultiMail($this->input));
        }

    }
}