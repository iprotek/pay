<?php

namespace iProtek\Pay\Models\Auths;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
//use Laravel\Sanctum\HasApiTokens;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class Admin extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable,SoftDeletes;


	
	/**
     * The attributes that are mass assignable.
     *
     * @var string
     */
	//use Notifiable;

	protected $guard = "admin";
	
	//protected $table = 'user_admins';
	protected $table = 'user_admins';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'company_id',
        'name',
        'email',
        'password',
        'user_type',
        'lang',
        'can_classify',
        'can_evaluate',
        'can_approve',
        'can_implement',
        'region',
        'is_verified',
        'email_verified_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\AdminPasswordResetNotification($token, $this->email));
    }

    
    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\AdminVerifyEmailNotification($this->email));
    }
}
