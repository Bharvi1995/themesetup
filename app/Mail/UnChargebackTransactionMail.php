<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UnChargebackTransactionMail extends Mailable
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
        $data = $this->subject('Removed Chargeback Transaction')
            ->markdown('emails.unChargebackTransactionMail')
            ->with(
                [
                    'card_type' => $this->details['card_type'],
                    'order_id' => $this->details['order_id'],
                    'card_no' => $this->details['card_no'],
                    'chargebacks_date' => $this->details['chargebacks_date'],
                    'amount' => $this->details['amount'],
                    'currency' => $this->details['currency'],
                ]
            );
        return $data;    
    }
}
