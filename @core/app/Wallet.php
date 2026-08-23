<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $table = 'wallets';

    protected $fillable = [
        'user_id', 'campaign_id', 'address', 'label', 'network', 'balance', 'is_default',
    ];

    protected $casts = [
        'balance' => 'decimal:8',
        'is_default' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function campaign()
    {
        return $this->belongsTo(Cause::class, 'campaign_id');
    }

    public function getShortAddressAttribute()
    {
        return substr($this->address, 0, 6) . '...' . substr($this->address, -4);
    }
}
