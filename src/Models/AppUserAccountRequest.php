<?php

namespace iProtek\Pay\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppUserAccountRequest extends Model
{
    use HasFactory;


    protected $fillable = [
        "oauth_client_id",
        "request_email",
        "request_group_id",
        "request_app_user_id",
        "requestor_app_user_id",
        "requestor_app_user_email",
        "requestor_app_user_account_id",
        "requestor_message",
        "accepted_at",
        "declined_at"
    ];


}
