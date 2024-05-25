<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class TicketReplyByUser extends Mailable {

    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    private $ticket;
    private $user;
    const url = '/admin/ticket';
    public function __construct($ticket,$user) {
        $this->ticket = $ticket;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        $data = $this->subject('Reply to Ticket!')
                    ->markdown('emails.ticket-reply-user')
                    ->with([
                        'reply'=>request()->reply,
                        'user'=>$this->user,
                        'ticket'=>$this->ticket,
                        'url'=>url(self::url)
                    ]);
                    
        return $data;
    }

}
