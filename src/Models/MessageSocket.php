<?php

namespace iProtek\Pay\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MessageSocket extends Model
{
    use HasFactory, SoftDeletes;
    
    public $fillable = [
        "user_id",
        "oauth_client_id",
        "name", //PUSHER.COM
        "is_active",
        "socket_app_id",
        "socket_settings"
    ];

    public $casts = [
        "socket_settings"=>"json",
        "is_active"=>"boolean"
    ];
}
