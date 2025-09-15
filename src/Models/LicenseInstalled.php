<?php

namespace iProtek\Pay\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LicenseInstalled extends Model
{
    use HasFactory, SoftDeletes;

    public $fillable = [
        "app_license_id",
        "mac_id",
        "computer_name",
        "computer_ip",
        "last_checked_at"
    ];
}
