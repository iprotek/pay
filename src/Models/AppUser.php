<?php

namespace iProtek\Pay\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use iProtek\Pay\Models\Application;
use iProtek\Pay\Models\UserAdmin;

class AppUser extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "account_no",
        "first_name",
        "last_name",
        "display_name",
        "contact_no",
        "email",
        "company",
        "created_by",
        "updated_by",
        "deleted_by",
        "address",
        "is_email_verified",
        "is_contact_verified",
        "password",
        "email_verified_at",
        "remember_token",
        "contact_no_verified_at"
    ];

    public $appends = [
        "app_purchased_count"
    ];

    public function getAppPurchasedCountAttribute(){
        $apps = AppLicense::where('app_user_id', $this->id);
        return $apps->count();
    }

    public function registerByInfo(){
        return $this->belongsTo(UserAdmin::class, 'created_by');
    }
    public function licenses(){
        return $this->hasMany(AppLicense::class, 'app_user_id')->select('id','app_id','app_user_id','expired_at','created_at', 'updated_at', 'is_trial', 'cost', 'bundle_cost', 'trial_at', 'purchased_at', 'activated_at')->withTrashed();
    }


}
