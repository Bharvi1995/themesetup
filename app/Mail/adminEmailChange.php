<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class adminEmailChange extends Mailable
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
        return $this->subject('Email Change Confirmation')
                    ->markdown('emails.adminEmailChange')
                    ->with([
                        'token'=>$this->content['token'],
                        'name'=>$this->content['name'],
                        'id' =>$this->content['id'],
                        'email' =>$this->content['email'],
                    ]);
    }
}
