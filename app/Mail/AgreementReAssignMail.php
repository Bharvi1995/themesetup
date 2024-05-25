<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AgreementReAssignMail extends Mailable
{
    use Queueable, SerializesModels;

    public $details;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data = $this->subject('Resubmit your Agreement')
            ->markdown('emails.agreementReAssignMail')
            ->with(
                [
                    'url' => $this->details['url'],
                    'reason' => $this->details['reason']
                ]
            );

        return $data;
    }
}
