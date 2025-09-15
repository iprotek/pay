<?php

namespace iProtek\Pay\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XracUserRole extends Model
{
    use HasFactory;

    protected $fillable = [
        "domain",
        "local_url",
        "app_user_account_id",
        "oauth_client_id",
        "local_branch_id",
        "local_system_id",
        "local_role_id",

        "is_default",
        "data",
        "is_active"
    ];
    
}
