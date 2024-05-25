<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\AnonymousNotifiable;
use App\Ticket;

class TicketAssign extends Notification
{
    use Queueable;
    public $ticket,$user_type;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Ticket $ticket,$user_type)
    {
        $this->ticket = $ticket;
        $this->user_type = $user_type;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ($notifiable instanceof AnonymousNotifiable)?['mail']:['broadcast','mail'];
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
                    ->line('New Ticket has been assigned to you.')
                    ->line('Ticket Title:'.$this->ticket->title)
                    ->action('View', url('/'.$this->user_type.'/ticket/'.$this->ticket->id));
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
                'ticket'=>$this->ticket,
                'url'=> '/'.$this->user_type.'/ticket/'.$this->ticket->id,
                'icon'=> 'mdi-ticket',
                'icon_bg'=>'bg-warning'
            ]
        ];
    }

    public function toDatabase($notifiable)
    {
        return [
            'ticket'=>$this->ticket,
            'url'=> '/'.$this->user_type.'/ticket/'.$this->ticket->id,
            'icon'=> 'mdi-ticket',
            'icon_bg'=>'bg-warning'
        ];
    }
}
