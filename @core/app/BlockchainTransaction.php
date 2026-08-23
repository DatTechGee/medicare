<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BlockchainTransaction extends Model
{
    protected $table = 'blockchain_transactions';

    protected $fillable = [
        'cause_log_id', 'campaign_id', 'wallet_address', 'transaction_hash',
        'amount', 'currency', 'network', 'transaction_type', 'status',
        'block_number', 'gas_fee', 'confirmed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'confirmed_at' => 'datetime',
    ];

    public function causeLog()
    {
        return $this->belongsTo(CauseLogs::class, 'cause_log_id');
    }

    public function campaign()
    {
        return $this->belongsTo(Cause::class, 'campaign_id');
    }

    public function getShortHashAttribute()
    {
        return substr($this->transaction_hash, 0, 10) . '...' . substr($this->transaction_hash, -8);
    }

    public function getShortWalletAttribute()
    {
        return substr($this->wallet_address, 0, 6) . '...' . substr($this->wallet_address, -4);
    }

    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 8) . ' ' . $this->currency;
    }
}
