<?php

namespace iProtek\Pay\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use iProtek\Pay\Notifications\AppUserAccountInvitationNotification;

class AppUserAccountInvitation extends Model
{
    use HasFactory, Notifiable;
 
    protected $fillable = [
        "app_user_id",
        "oauth_client_id",
        "app_name",
        "email",
        "role",
        "group_id",
        "app_user_account_id",
        "group_name",
        "accepted_at",
        "declined_at"
    ];

    public function notifyInvitation(){
        \iProtek\Pay\Helpers\NotificationHelper::send($this, new AppUserAccountInvitationNotification());
    }
    
}
