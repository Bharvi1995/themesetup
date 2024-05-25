<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ApplicationReferredToBankMail extends Mailable
{
    use Queueable, SerializesModels;

    public $bank;
    public $application;
    public $application_assign_to_bank;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($bank, $application, $application_assign_to_bank)
    {
        $this->bank = $bank;
        $this->application = $application;
        $this->application_assign_to_bank = $application_assign_to_bank;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data = $this->subject('Application Referred')
            ->markdown('emails.applicationReferredToBankMail')
            ->with(
                [
                    'bank_name' => $this->bank['bank_name'],
                    'company_name' => $this->application['business_name'],
                    'merchant_name' => $this->application->user->name,
                    'merchant_email' => $this->application->user->email,
                    'referred_note' => $this->application_assign_to_bank->referred_note,
                    'assign_date' => date('Y-m-d',strtotime($this->application_assign_to_bank->created_at)),
                ]
            );
        return $data;
    }
}
