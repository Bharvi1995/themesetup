<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\AnonymousNotifiable;
use App\Transaction;

class UserClaimRefund extends Notification
{
    use Queueable;
    public $transaction;
    const url = '/transactions';
    const icon = 'ft-credit-card';
    const color = 'danger';
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database','broadcast','mail'];
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
                    ->subject('Refund Transaction')
                    ->markdown('emails.notifications.claim-refund',['transaction'=>$this->transaction]);
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
                'transaction'=>$this->transaction
            ]
        ];
    }

    public function toDatabase($notifiable)
    {
        return [
            'transaction'=>$this->transaction
        ];
    }
}
