<?php

namespace App\Notifications;

use App\Application;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BankApplicationResubmited extends Notification
{
    use Queueable;
    public $content;
    const url = 'admin/applications-bank/pending';
    const icon = 'ft-layers';
    const color = 'success';
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($content)
    {
        $this->content = $content;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
        // return ['database','broadcast','mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Bank application resubmited.')
            ->markdown('emails.bankapplication.application_submitted',
                        [
                            'name'=>$this->content['company_name'],
                            'company_address'=>$this->content['company_address'],
                            'phone_number'=>$this->content['phone_number'],
                            'email'=>$this->content['email']
                        ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toBroadcast($notifiable)
    {
        return [
            'data' => [
                'content' => $this->content,
            ]
        ];
    }

    public function toDatabase($notifiable)
    {
        return [
            'content' => $this->content,
        ];
    }
}
