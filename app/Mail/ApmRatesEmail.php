<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApmRatesEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected $userApms;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->userApms = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("New APM & Rates")->markdown('emails.userApmRatesMail')->with("userApms", $this->userApms);
    }
}