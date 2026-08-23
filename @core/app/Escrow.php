<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Escrow extends Model
{
    protected $table = 'escrow';

    protected $fillable = [
        'campaign_id',
        'cause_log_id',
        'amount',
        'donor_wallet_address',
        'blockchain_tx_hash',
        'status',
        'released_at',
    ];

    public function campaign()
    {
        return $this->belongsTo(Cause::class, 'campaign_id');
    }

    public function donation()
    {
        return $this->belongsTo(CauseLogs::class, 'cause_log_id');
    }

    public function getFormattedAmountAttribute()
    {
        return amount_with_currency_symbol($this->amount);
    }
}
