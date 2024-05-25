<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ShowReport extends Mailable
{
    use Queueable, SerializesModels;

    public $fileName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $location = storage_path("app/public/pdf/".$this->fileName);
        $data = $this->subject('Merchant Settlement Report')
            ->markdown('emails.show-report')
            ->attach($location);
        
        return $data; 
    }
}
