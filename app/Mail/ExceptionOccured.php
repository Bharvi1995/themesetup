<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;

class ExceptionOccured extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The body of the message.
     *
     * @var string
     */
    public $content;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($content,$css)
    {
        if (auth()->guard('admin')->check()) {
            $role_name = 'Admin - '.auth()->guard('admin')->user()->email; 
        } elseif (auth()->check()) {
            $role_name = 'Merchant -'.auth()->user()->email;
        } elseif (auth()->guard('agentUser')->check()) {
            $role_name = 'RP -'.auth()->guard('rp')->user()->email;  
        } 
        else {
            $role_name = 'Guest';
        }

        $subject = env('ERROR_MAIL_SUBJECT') ?? 'testpay Error By';

        $this->subject = $subject.' '.$role_name;
        $this->content = $content;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->content.= '<br><br> <strong> REQUEST INPUTS : </strong> <br>'.(string) json_encode(\Request::all()); 

        return $this->view('emails.exception')
                    ->with('content', $this->content);
    }
}