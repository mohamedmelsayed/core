<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\Searchable;
use App\Traits\UserNotify;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable {
    use HasApiTokens, Searchable, UserNotify;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'ver_code', 'kyc_data',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'address'           => 'object',
        'ver_code_send_at'  => 'datetime',
    ];

    public function loginLogs() {
        return $this->hasMany(UserLogin::class);
    }

    public function deposits() {
        return $this->hasMany(Deposit::class)->where('status', '!=', Status::PAYMENT_INITIATE);
    }

    public function deviceTokens() {
        return $this->hasMany(DeviceToken::class);
    }
    public function devices() {
        return $this->hasMany(UserDevice::class);
    }

    public function plan() {
        return $this->belongsTo(Plan::class);
    }

    public function tickets() {
        return $this->hasMany(SupportTicket::class);
    }

    public function fullname(): Attribute {
        return new Attribute(
            get: fn() => $this->firstname . ' ' . $this->lastname,
        );
    }
    // SCOPES
    public function scopeActive($query) {
        return $query->where('status', Status::USER_ACTIVE)->where('ev', Status::VERIFIED)->where('sv', Status::VERIFIED);
    }

    public function scopeBanned($query) {
        return $query->where('status', Status::USER_BAN);
    }

    public function scopeEmailUnverified($query) {
        return $query->where('ev', Status::NO);
    }

    public function scopeMobileUnverified($query) {
        return $query->where('sv', Status::NO);
    }

    public function scopeEmailVerified($query) {
        return $query->where('ev', Status::VERIFIED);
    }

    public function scopeMobileVerified($query) {
        return $query->where('sv', Status::VERIFIED);
    }

    public function scopeAdsFree($query) {
        return $query->where('ads_free', Status::ENABLE)->where('ads_free_expired', '>', now());
    }


    public function hasVerifiedEmail()
    {
        return true;
       // return $this->ev;
    }

}
