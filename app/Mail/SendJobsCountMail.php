<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendJobsCountMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $jobsCount, $failedJobsCount;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($jobsCount, $failedJobsCount)
    {
        $this->jobsCount = $jobsCount;
        $this->failedJobsCount = $failedJobsCount;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("Jobs & failed jobs table count")->markdown('emails.jobCountMail')->with(["jobsCount" => $this->jobsCount, "failedJobsCount" => $this->failedJobsCount]);
    }
}