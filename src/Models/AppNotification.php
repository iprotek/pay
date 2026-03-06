<?php

namespace iProtek\Pay\Models;

use Illuminate\Database\Eloquent\Model;

class AppNotification extends Model
{
    //
    
    protected $fillable = [

        "domain",
        "oauth_client_id",
        "pay_account_id",
        
        "local_branch_id",
        "local_system_name",

        "notice_count"

    ];
}
