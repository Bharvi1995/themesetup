<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ApplicationAssignToBankMail extends Mailable
{
    use Queueable, SerializesModels;

    public $application,$bank;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($application,$bank)
    {
        $this->application = $application;
        $this->bank = $bank;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data = $this->subject('Application Assign')
            ->markdown('emails.applicationAssignToBankMail')
            ->with(
                [
                    'userName' => $this->application['userName'],
                    'email' => $this->application['email'],
                    'business_name' => $this->application['business_name'],
                    'bank_name' => $this->bank[0]->application->company_name
                ]
            );
        return $data;
    }
}
