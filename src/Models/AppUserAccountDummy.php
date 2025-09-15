<?php

namespace iProtek\Pay\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail; 
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as A2;
//use Laravel\Sanctum\HasApiTokens;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use DB;

class AppUserAccountDummy extends Model
{ 
    public $table = "app_user_accounts";
    use HasFactory;
    public $fillable = [
        'app_user_id',
        'email',  //This is just a pseudo account
        'name',
        'email_verified_at', //This is just a pseudo account
        'oath_client_id',
        'is_blocked',
        'blocked_by',
        'provider'
    ]; 

    protected $hidden = [
        "password",
        "oath_client_id",
        "email_verified_at",
        'remember_token'
    ];
}
