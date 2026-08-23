<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CauseLogs extends Model
{
    protected $table = 'cause_logs';
    protected $fillable = ['cause_id','email','name','status','amount','transaction_id','payment_gateway','track','user_id','anonymous','admin_charge'
        ,'donation_withdraw_id','reward_point','reward_amount','gift_id','address','phone','recuring_token','recuring_token_verify','custom_fields','attachments','added_in_raised_amount',
        'donor_wallet_address','blockchain_transaction_hash','payment_type','donation_status'];

    public function cause(){
        return $this->belongsTo('App\Cause','cause_id');
    }

    public function user(){
        return $this->belongsTo('App\User','user_id');
    }

    public function withdraw(){
        return $this->belongsTo('App\DonationWithdraw','donation_withdraw_id');
    }

    public function gift()
    {
        return $this->belongsTo(Gift::class);
    }


    public function recurings()
    {
        return $this->hasMany(Recuring::class,'cause_log_id','id');
    }

    public function blockchain_transaction()
    {
        return $this->hasOne(BlockchainTransaction::class, 'cause_log_id');
    }

    public function getShortTxHashAttribute()
    {
        if (empty($this->blockchain_transaction_hash)) return '-';
        return substr($this->blockchain_transaction_hash, 0, 10) . '...' . substr($this->blockchain_transaction_hash, -8);
    }

}
