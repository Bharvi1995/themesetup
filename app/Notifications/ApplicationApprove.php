<?php

namespace App\Notifications;

use App\Application;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationApprove extends Notification implements ShouldQueue
{
    use Queueable;
    public $application, $user;
    const url = '/my-application';
    const icon = 'ft-layers';
    const color = 'success';
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Application $application, User $user)
    {
        $this->application = $application;
        $this->user = $user;
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
            ->subject('testpay Account Request - Approval Intimation')
            ->markdown('emails.notifications.application_approve', ['user' => $this->user, 'url' => url(self::url)]);
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
                'application' => $this->application,
            ]
        ];
    }

    public function toDatabase($notifiable)
    {
        return [
            'application' => $this->application,
        ];
    }
}
