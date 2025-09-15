<?php

namespace iProtek\Pay\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XracDomain extends Model
{
    use HasFactory;

    protected $fillable = [
        "app_user_account_id",
        "oauth_client_id",
        "local_system_id",
        "local_url",

        "name"
    ];
}
