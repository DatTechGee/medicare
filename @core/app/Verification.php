<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Verification extends Model
{
    protected $table = 'verifications';

    protected $fillable = [
        'campaign_id', 'type', 'status', 'verified_by', 'notes', 'document_path',
    ];

    public function campaign()
    {
        return $this->belongsTo(Cause::class, 'campaign_id');
    }

    public function getStatusColorAttribute()
    {
        $colors = ['pending' => 'warning', 'verified' => 'success', 'rejected' => 'danger'];
        return $colors[$this->status] ?? 'secondary';
    }
}
