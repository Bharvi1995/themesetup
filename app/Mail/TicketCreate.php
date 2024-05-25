<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class TicketCreate extends Mailable {

    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    private $user;
    private $ticket;
    public function __construct($ticket,$user) {
        $this->user = $user;
        $this->ticket = $ticket;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        $data = $this->subject('New Ticket!')
                    ->markdown('emails.ticket-admin')
                    ->with([
                        'ticket'=>$this->ticket,
                        'user'=>$this->user
                    ]);
                    
        return $data;
    }

}
