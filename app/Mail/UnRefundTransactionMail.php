<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UnRefundTransactionMail extends Mailable
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
        $data = $this->subject('Removed Refund Transaction')
            ->markdown('emails.unRefundTransactionMail')
            ->with(
                [
                    'card_type' => $this->details['card_type'],
                    'card_no' => $this->details['card_no'],
                    'refund_date' => $this->details['refund_date'],
                    'amount' => $this->details['amount'],
                    'currency' => $this->details['currency'],
                    'first_name' => $this->details['first_name'],
                    'last_name' => $this->details['last_name'],
                    'order_id' => $this->details['order_id'],
                    'email' => $this->details['email'],
                    'created_at' => $this->details['created_at']
                ]
            );
        return $data;    
    }
}
