<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UnRetrievalTransactionMail extends Mailable
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
        $data = $this->subject('Removed Retrieval Transaction')
            ->markdown('emails.unRetrievalTransactionMail')
            ->with(
                [
                    'card_type' => $this->details['card_type'],
                    'card_no' => $this->details['card_no'],
                    'retrieval_date' => $this->details['retrieval_date'],
                    'amount' => $this->details['amount'],
                    'currency' => $this->details['currency'],
                ]
            );
        return $data;    
    }
}
