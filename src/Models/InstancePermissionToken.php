<?php

namespace iProtek\Pay\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstancePermissionToken extends Model
{
    use HasFactory;

    protected $fillable = [
        "guard",
        "name",
        "scopes",
        "user_admin_id",
        "session_id",
        "access_token",
        "expires_at",
    ];

    protected $casts = [
        "scopes"=>"json"
    ];
}
