<?php

namespace iProtek\Pay\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;
use Laravel\Passport\Client;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AppUserAccountRecoveryNotification extends Notification
{
     
    //public $email = '';
    public $app = null;
    public $redirect_url = null;

    public function __construct(Client $app)
    {
        $this->app = $app;
        $this->redirect_url = request()->input('redirect_url') ?? "";
        //$this->token = $token;
        //$this->email = $email;
    }

    public function toMail($notifiable)
    {
        //$url = route('customer.password.reset', ["token"=>$this->token,"email"=>$this->email]);
        
        $message = (new MailMessage)
            //->from(config('mail.from.address'), config('mail.from.name'))
            ->subject('User Account Recovery')
            ->line('Hi, '.$notifiable->name.' we would like to inform you that you have a password reset request.')
            ->line('Application#: '.$this->app->id)
            ->line('Application: '.$this->app->name)
            ->line('Email: '.$notifiable->email) 
            ->line(' If you did not make this action, no further taken required. Otherwise.. ')
            ->action('Reset Password', $this->verificationUrl($notifiable))
            ->line('Thank you!');

        return $message;
    }
    
    public function verificationUrl($notifiable)
    { 
        return URL::temporarySignedRoute(
            'get-app-user-account-recovery',
            now()->addMinutes(1440),
            [
                'app_user_account_id' => $notifiable->id,
                'email'=>$notifiable->email,
                'client_id'=>$this->app->id,
                'redirect_url'=>$this->redirect_url
            ]
        ); 
    }
    public function via ($notifiable) {
        return ['mail'];
    }

}
