<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Milestone extends Model
{
    protected $fillable = [
        'campaign_id',
        'title',
        'description',
        'amount',
        'status',
        'proof_document',
        'proof_notes',
        'verified_at',
        'released_at',
        'verified_by',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'released_at' => 'datetime',
    ];

    public function campaign()
    {
        return $this->belongsTo(Cause::class, 'campaign_id');
    }

    public function verifier()
    {
        return $this->belongsTo(Admin::class, 'verified_by');
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'pending' => 'warning',
            'proof_submitted' => 'info',
            'verified' => 'success',
            'released' => 'success',
            'rejected' => 'danger',
        ];
        return $colors[$this->status] ?? 'secondary';
    }
}
