<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApplicationNoteBankToAdminMail extends Mailable
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
        return $this->subject('Current Status Updated on Application')
                    ->markdown('emails.applicationNoteBankToAdminMail')
                    ->with([
                        'note'=>$this->content['note'],
                        'business_name'=>$this->content['business_name'],
                        'bank_company_name'=>$this->content['bank_company_name']
                    ]);
    }
}
