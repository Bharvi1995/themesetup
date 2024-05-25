<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CrossSignedAgreementSentMailRP extends Mailable
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
        $data = $this->subject(config("app.name").' Cross Signed Agreement')
            ->markdown('emails.crossSignedAgreementSentMailRP')
            ->with(
                [
                    'name' => $this->details['name']
                ]
            );
        if(!empty($this->details['file'])){
            $data = $data->attach(asset($this->details['file']),['as'=>'Cross_Signed_Agreement_testpay.pdf']);
        };
        return $data;
    }
}
