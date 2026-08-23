<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable,HasApiTokens;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','email_verified','email_verify_token','phone','address','state','city','zipcode','country_id','username','image','facebook_id','google_id',
        'monthly_income','annual_income','income_source','nid_image','driving_license_image','passport_image','tax_verify_status',
        'wallet_address','wallet_connected_at','wallet_verified','wallet_verified_at','wallet_verified_by','role','demo_eth_balance','status','campaign_permission'
    ];

    protected static function booted()
    {
        /* every account is auto-generated a blockchain wallet at registration */
        static::created(function ($user) {
            if (empty($user->wallet_address)) {
                ensure_user_wallet($user);
            }
        });
    }


    protected $hidden = [
        'password', 'remember_token',
    ];


    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function isPatient()
    {
        return $this->role === 'patient';
    }

    public function isDonor()
    {
        return $this->role !== 'patient';
    }

    public function patientProfile()
    {
        return $this->hasOne(PatientProfile::class);
    }

    public function wallets()
    {
        return $this->hasMany(Wallet::class);
    }
}
