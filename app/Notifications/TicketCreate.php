<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\AnonymousNotifiable;
use App\Ticket;

class TicketCreate extends Notification
{
    use Queueable;
    public $ticket;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ($notifiable instanceof AnonymousNotifiable) ? ['mail'] : ['database', 'broadcast'];
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
            ->line('New Ticket has been raised.')
            ->line('Ticket Title:' . $this->ticket->title)
            ->action('View', url('/admin/ticket/' . $this->ticket->id));
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
                'ticket' => $this->ticket,
                'url' => '/admin/ticket/' . $this->ticket->id,
                'icon' => 'mdi-ticket',
                'icon_bg' => 'bg-danger'
            ]
        ];
    }

    public function toDatabase($notifiable)
    {
        return [
            'ticket' => $this->ticket,
            'url' => '/admin/ticket/' . $this->ticket->id,
            'icon' => 'mdi-ticket',
            'icon_bg' => 'bg-danger'
        ];
    }
}
