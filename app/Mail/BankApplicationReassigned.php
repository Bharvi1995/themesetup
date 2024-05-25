<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BankApplicationReassigned extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($content)
    {
        $this->content = $content;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data = $this->subject('Application Reassigned for '.config("app.name").'.')
            ->markdown('emails.bankapplication.application_reassigned')
            ->with(
                [
                    'name'=>$this->content['user_name'],
                    'reason'=>$this->content['reason']
                ]
            );
            
        return $data;
    }
}
