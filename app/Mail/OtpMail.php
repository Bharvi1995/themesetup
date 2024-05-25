<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class OtpMail extends Mailable
{

    use Queueable,
        SerializesModels;

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
        \Log::info([
            'OTP Send'
        ]);
        $data = $this->subject('OTP confirmation for your ' . config("app.name") . ' Account')
            ->markdown('emails.otp')
            ->with(["user" => $this->user]);

        return $data;
    }
}
