<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class TicketReplyByAdmin extends Mailable {

    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    private $ticket;
    const url = '/ticket';
    public function __construct($ticket) {
        $this->ticket = $ticket;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        $data = $this->subject('Reply to Ticket !!!')
                    ->markdown('emails.ticket-reply-admin')
                    ->with([
                        'ticket'=>$this->ticket,
                        'url'=>url(self::url)
                    ]);
                    
        return $data;
    }

}
