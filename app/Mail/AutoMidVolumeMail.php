<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AutoMidVolumeMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $data, $date;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $date)
    {
        $this->data = $data;
        $this->date = $date;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('testpay Volume')
            ->markdown('emails.notifications.auto_mid_volume_mail')->with(["data" => $this->data, 'date' => $this->date]);
    }
}
