<?php

namespace App\Notifications;

use App\Application;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationResubmit extends Notification
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
            ->subject('Incomplete Application with '.config("app.name").'.')
            ->line('Dear Team,')
            ->line('New user has just registered on the website.')
            ->line('Pending Approval - There is an on site work for you to approve. (Please go down to select Approve or Reject)')
            // ->line($this->application->reason_reassign)
            ->action('Application', url('/admin/applications-list/view/' . $this->application->id))
            ->line("Please ignore, if it's already done.");
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
