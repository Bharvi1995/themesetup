<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BankOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    private $user;
    public function __construct($user)
    {
        $this->user = $user;
        
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data = $this->subject('OTP confirmation alert for your '.config("app.name").' Account')
                    ->markdown('emails.otp_bank')
                    ->with([
                        "title" => "OTP",
                        "name" => $this->user['name'],
                        "login_otp" => $this->user['otp'],
                    ]);
                    
        return $data;
    }
}
