<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BankApplicationReSubmited extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($content)
    {
        $this->content = $content;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Bank application resubmitted.')
                    ->markdown('emails.bankapplication.application_resubmitted')
                    ->with([
                        'name'=>$this->content['company_name'],
                        'company_address'=>$this->content['company_address'],
                        'phone_number'=>$this->content['phone_number'],
                        'email'=>$this->content['email'],
                    ]);
    }
}
