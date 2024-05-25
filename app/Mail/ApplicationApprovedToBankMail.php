<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ApplicationApprovedToBankMail extends Mailable
{
    use Queueable, SerializesModels;

    public $bank;
    public $application;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($bank, $application)
    {
        $this->bank = $bank;
        $this->application = $application;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data = $this->subject('Application Approved')
            ->markdown('emails.applicationApprovedToBankMail')
            ->with(
                [
                    'bank_name' => $this->bank['bank_name'],
                    'business_name' => $this->application['business_name']
                ]
            );
        return $data;
    }
}
