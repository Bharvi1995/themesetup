<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserClaimRefund extends Mailable
{
    use Queueable, SerializesModels;

    protected $transaction;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Refund Transaction')
            ->markdown('emails.notifications.claim-refund')
            ->with(['transaction' => $this->transaction]);
    }
}