<?php

namespace iProtek\Pay\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class XracBranch extends Model
{
    use HasFactory, SoftDeletes;


    protected $fillable = [
        "domain",
        "oauth_client_id",
        "local_branch_id",
        "local_system_id",
        "local_url", 

        "name",
        "is_active",
        "deleted_at",
        
        "created_pay_user_account_id",
        "updated_pay_user_account_id",
        "deleted_pay_user_account_id"
    ];

    protected $casts = [
        "is_active"=>"boolean",
        "deleted_at"=>"datetime:Y-m-d H:i:s"
    ];
}
