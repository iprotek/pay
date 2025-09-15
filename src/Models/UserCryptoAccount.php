<?php

namespace iProtek\Pay\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserCryptoAccount extends Model
{
    use HasFactory, SoftDeletes;
    public $fillable = [
        'user_id',
        'crypto_address',
        'private_key',
        'password',
        'is_external',
        'created_by',
        'updated_by',
        'is_default',
        'provider'
    ];
}
