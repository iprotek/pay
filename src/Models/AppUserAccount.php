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
use Laravel\Passport\Client;
use iProtek\Pay\Notifications\AppUserAccountRecoveryNotification;

class AppUserAccount extends Authenticatable implements A2
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasFactory;
    public $fillable = [
        'app_user_id',
        'email',  //This is just a pseudo account
        'name',
        'email_verified_at', //This is just a pseudo account
        'oauth_client_id',
        'is_blocked',
        'blocked_by',
        'provider',
        'add_app_user_by',
        'add_admin_user_by',
        'group_name',
        'password'
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime'
    ];

    protected $appends = [
        "groups",
        "own_group",
        "count_members"
    ];

    public function getGroupsAttribute(){
        
        $app_user_id = $this->app_user_id;
        $cliend_id =  $this->oauth_client_id;
        //if(!$cliend_id){
            //return AppUserAccountGroup::where('app_user_id', $app_user_id)->with('owner')->get();
        //}
        return AppUserAccountGroup::with('owner')->where('app_user_id', $app_user_id)->where('oauth_client_id', $cliend_id)->where('is_blocked', 0)->get();

    }

    public function getOwnGroupAttribute(){
        $id = $this->id;
        $app_user_id = $this->app_user_id;
        return AppUserAccountGroup::where('group_id', $id)->where('app_user_id', $app_user_id)->first();
    }

    public function getCountMembersAttribute(){
        $id = $this->id;
        return AppUserAccountGroup::where('group_id', $id)->count();
    }

    public function findForPassport($username)
    {
        //Get specific client_id        
        $cliend_id =  request('client_id');  

        return $user = (new self)->where('email', $username)->with(['group_accounts'])->where('is_blocked', 0)->where('oauth_client_id', $cliend_id)->first();//->where('is_active', 1)->first();
    }

    public function group_accounts(){
        return $this->hasMany(AppUserAccountGroup::class,'app_user_id')->with(['owner']);
    }

    public function group_members(){
        return $this->hasMany(AppUserAccountGroup::class, 'group_id');
    }
    public function oauth_client(){
        return $this->belongsTo(Client::class, 'oauth_client_id');
    }

    
    public function isPasswordCorrect($password)
    {
        return \Illuminate\Support\Facades\Hash::check($password, $this->password);
    }
    public function updatePassword($newPassword)
    {
        $this->password = \Illuminate\Support\Facades\Hash::make($newPassword);
        //Notify App User for the updated password
        $this->save();
    }

    
    public function sendRecoveryLink(){
        $client_id = $this->oauth_client_id;
        $cl = Client::find($client_id); 
        \iProtek\Pay\Helpers\NotificationHelper::send($this, new AppUserAccountRecoveryNotification($cl));
    }
}
