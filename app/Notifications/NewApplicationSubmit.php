<?php

namespace App\Notifications;

use App\Application;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewApplicationSubmit extends Notification
{
    use Queueable;
    public $application;
    const url = '/admin/inprogress-applications';
    const icon = 'ft-layers';
    const color = 'success';
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
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
            ->subject('New Application Submitted')
            ->markdown(
                'emails.notifications.new_application_admin',
                ['url' => url('/admin/applications-list/view/' . $this->application->id), 'skype' => $this->application->skype_id, 'email' => $this->application->user->email]
            );
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
                'application' => $this->application
            ]
        ];
    }

    public function toDatabase($notifiable)
    {
        return [
            'application' => $this->application
        ];
    }
}
