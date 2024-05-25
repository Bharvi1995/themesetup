<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomResetPasswordNotification extends Notification
{
    use Queueable;
    public $userName;
    public $token;
    public $email;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($userName, $token, $email)
    {
        $this->userName = $userName;
        $this->token = $token;
        $this->email = $email;
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
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $link = url("/password/reset/" . $this->token . '?email=' . $this->email);
        return (new MailMessage)
            ->subject('Password Reset Link for Your ' . config('app.name') .  ' Account')
            ->greeting('Dear ' . $this->userName . ',')
            ->line('You have initiated a password reset for your ' . config('app.name') . ' account. To complete the process, please click on the link below. This link will remain valid for the next 24 hours:')
            ->action('Reset Password', $link)
            ->line("If you did not make this request or have any concerns, please don't hesitate to reach out to our customer support team.")
            ->line('Thank you for choosing ' . config('app.name') . '  as your payment processing partner.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
