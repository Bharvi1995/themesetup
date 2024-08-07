<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PreArbitrationNoticeMail extends Mailable
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
        $data = $this->subject('Pre Arbitration Notice')
            ->markdown('emails.preArbitrationSentMail')
            ->with(
                [
                	'business_name' => $this->details['business_name'],
                	'first_name' => $this->details['first_name'],
                	'last_name' => $this->details['last_name'],
                	'card_no' => $this->details['card_no'],
                	'order_id' => $this->details['order_id'],
                	'currency' => $this->details['currency'],
                    'amount' => $this->details['amount']
                ]
            )->cc(['info.paylaksa@gmail.com']);

            if(!empty($this->details['file'])){
                $data = $data->attach(asset($this->details['file']),['as'=>'DISPUTED_TRANSACTION_testpay.pdf']);
            };

        return $data;
    }
}
