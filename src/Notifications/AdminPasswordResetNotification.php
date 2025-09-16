<?php

namespace iProtek\Pay\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AdminPasswordResetNotification extends Notification
{
    
    public $token;
    public $email;

    public function __construct($token, $email)
    {
        $this->token = $token;
        $this->email = $email;
    }

    public function toMail($notifiable)
    {
        $url = route('password.reset', ["token"=>$this->token,"email"=>$this->email]);

        return (new MailMessage)
            //->from(config('mail.from.address'), config('mail.from.name'))
            ->line('You are receiving this email because we received a password reset request for your account.')
            ->action('Reset Password', $url)
            ->line('If you did not request a password reset, no further action is required.');
    }
    
    public function via ($notifiable) {
        return ['mail'];
    }

}
