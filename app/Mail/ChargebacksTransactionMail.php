<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ChargebacksTransactionMail extends Mailable
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
        $data = $this->subject('Alert: Chargeback Notification')
            ->markdown('emails.chargebacksTransactionMail')
            ->with(
                [
                    'chargebacks_date' => $this->details['chargebacks_date'],
                    'url' => $this->details['url'],
                    'card_type' => $this->details['card_type'],
                    'card_no' => $this->details['card_no'],
                    'amount' => $this->details['amount'],
                    'created_at' => $this->details['created_at'],
                    'currency' => $this->details['currency'],
                    'order_id' => $this->details['order_id'],
                    'first_name' => $this->details['first_name'],
                    'last_name' => $this->details['last_name'],
                    'email' => $this->details['email'],
                    'userName' => $this->details['userName']
                ]
            );

        return $data;
    }
}
