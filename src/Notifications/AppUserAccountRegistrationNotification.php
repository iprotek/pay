<?php

namespace iProtek\Pay\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;
use Laravel\Passport\Client;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AppUserAccountRegistrationNotification extends Notification
{
     
    //public $email = '';
    public $app = null;
    public function __construct(Client $app)
    {
        $this->app = $app;
        //$this->token = $token;
        //$this->email = $email;
    }

    public function toMail($notifiable)
    {
        //$url = route('customer.password.reset', ["token"=>$this->token,"email"=>$this->email]);
        
        return (new MailMessage)
            //->from(config('mail.from.address'), config('mail.from.name'))
            ->subject('App Registration')
            ->line('Hi, '.$notifiable->name.' we would like to know if you would like to use this app.')
            ->line('Application ID: '.$this->app->id)
            ->line('Application: '.$this->app->name)
            ->line('Email: '.$notifiable->email)
            ->line('Please ignore the message if you did not intent for this action. ')
            ->line('But if you would like use the application, please click the button to proceed registration. ')
            ->action('Proceed Registration', $this->verificationUrl($notifiable))
            ->line('Thank you!');
            //url('/'.$notifiable->email))//
    }
    
    public function verificationUrl($notifiable)
    { 
        return URL::temporarySignedRoute(
            'get-app-user-account-registration',
            now()->addMinutes(60),
            [
                'app_user_account_registration_id' => $notifiable->id,
                'email'=>$notifiable->email,
                'client_id'=>$this->app->id
            ]
        ); 
    }
    public function via ($notifiable) {
        return ['mail'];
    }

}
