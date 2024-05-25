<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AgentApplicationResubmitMail extends Mailable
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
        return $this->subject('Application Resubmitted.')
                    ->markdown('emails.agentApplicationResubmitMail')
                    ->with([
                        'id'=>$this->content['id'],
                        'company_name'=>$this->content['company_name'],
                        'agent_name'=>$this->content['agent_name'],
                        'agent_email'=>$this->content['agent_email']
                    ]);
    }
}
