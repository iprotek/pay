<?php

namespace iProtek\Pay\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\Client;
use iProtek\Pay\Notifications\AppUserAccountRegistrationNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;

class AppUserAccountRegistration extends Model
{
    use HasFactory, Notifiable;
    
    protected $fillable = [
        "oauth_client_id",
        "email",
        "name",
        "accepted_at",
        "declined_at",
        "expired_at"
    ];
    

    public function notifyRegistration(){
        $client_id = $this->oauth_client_id;
        $cl = Client::find($client_id); 
        \iProtek\Pay\Helpers\NotificationHelper::send($this, new AppUserAccountRegistrationNotification($cl));
    }


}
