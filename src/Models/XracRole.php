<?php

namespace iProtek\Pay\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class XracRole extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "domain",
        "oauth_client_id",
        "local_role_id",
        "local_url",
        "name",
        "default_data",

        "description",
        "is_active",

        "created_pay_user_account_id",
        "updated_pay_user_account_id",
        "deleted_pay_user_account_id",
        "deleted_at"
    ];
    
    protected $casts = [
        "is_active"=>"boolean",
        "deleted_at"=>"datetime:Y-m-d H:i:s",
        "default_data"=>"json"
    ];
}
