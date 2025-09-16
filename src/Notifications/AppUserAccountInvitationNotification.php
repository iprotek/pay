<?php

namespace iProtek\Pay\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;
use Laravel\Passport\Client;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AppUserAccountInvitationNotification extends Notification
{
     
    //public $email = '';
    public function __construct()
    {
        //$this->token = $token;
        //$this->email = $email;
    }

    public function toMail($notifiable)
    {
        //$url = route('customer.password.reset', ["token"=>$this->token,"email"=>$this->email]);
        $app = Client::find($notifiable->oauth_client_id);

        return (new MailMessage)
            //->from(config('mail.from.address'), config('mail.from.name'))
            ->subject('App Invitation')
            ->line('You were invited to join this application.')
            ->line('Application ID: '.$app->id)
            ->line('Application: '.$app->name)
            ->line('Email: '.$notifiable->email)
            ->line('Workspace: '.$notifiable->group_name)
            ->line('Please ignore the message if you did not intent for this action. ')
            ->line('But if you like to join workspace please click the invitation button.')
            ->action('Proceed Invitation', $this->verificationUrl($notifiable))
            ->line('Thank you!');
            //url('/'.$notifiable->email))//
    }
    
    public function verificationUrl($notifiable)
    { 
        return URL::temporarySignedRoute(
            'get-app-user-account-invitation',
            now()->addMinutes(60),
            [
                'app_user_account_invitation_id' => $notifiable->id,
                'email'=>$notifiable->email,
                'client_id'=>$notifiable->oauth_client_id
            ]
        ); 
    }
    public function via ($notifiable) {
        return ['mail'];
    }

}
