<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\MailTemplates;

class SendUserMultiMail extends Mailable
{
    use Queueable, SerializesModels;

    public $details;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data = $this->subject($this->details['subject'])
            ->markdown('emails.send_user_multi_mail')
            ->with(
                [
                    'body' => $this->details['bodycontent'],
                ]
            );      

            $img = MailTemplates::where('id',$this->details['email_template'])->first();
            
            if(!empty($img['files'])){
                $detail['attach'] = json_decode($img['files']);
                foreach ($detail['attach'] as $key => $value) {
                    $data = $data->attach(getS3Url($value));
                }
            }

        return $data;    
    }
}
