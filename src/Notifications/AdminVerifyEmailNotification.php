<?php

namespace iProtek\Pay\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;

class AdminVerifyEmailNotification extends Notification
{
    
    public $email = '';

    public function __construct($email)
    {
        //$this->token = $token;
        $this->email = $email;
    }

    public function toMail($notifiable)
    {
        //$url = route('customer.password.reset', ["token"=>$this->token,"email"=>$this->email]);
        
        return (new MailMessage)
        //->from(config('mail.from.address'), config('mail.from.name'))
        ->subject('Customer Verification Email')
        ->line('Thank you for registering. Please click the button below to verify your email address.')
        ->action('Verify Email', $this->verificationUrl($notifiable) )
        ->line('If you did not create an account, no further action is required.');
    }
    
    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verify-email',
            now()->addMinutes(60),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
                'email'=>$this->email
            ]
        );
    }
    public function via ($notifiable) {
        return ['mail'];
    }

}
