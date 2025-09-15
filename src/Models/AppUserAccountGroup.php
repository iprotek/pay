<?php

namespace iProtek\Pay\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppUserAccountGroup extends Model
{
    use HasFactory;
    public $fillable = [
        'app_user_id',
        'oauth_client_id',
        'is_blocked',
        'accepted_at',
        'share_to_app_user_account_id',
        'declined_at',
        'group_id', //Uses the app_user_account_id from creator
        'role' //createor if the the user was the creator
    ];

    public function owner(){
        return $this->belongsTo(AppUserAccountDummy::class, 'group_id');
    }

    public function share_user(){
        return $this->belongsTo(AppUserAccountDummy::class, 'share_to_app_user_account_id');
    }


}
