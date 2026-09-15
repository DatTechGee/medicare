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

    public static function ensureForCampaign($campaignId)
    {
        if (!$campaignId) {
            return collect();
        }

        if (!\Illuminate\Support\Facades\Schema::hasTable('verifications')) {
            return collect();
        }

        $existing = self::where('campaign_id', $campaignId)->pluck('type')->all();
        $standardTypes = ['patient', 'hospital', 'document', 'amount'];
        $created = [];

        foreach ($standardTypes as $type) {
            if (!in_array($type, $existing, true)) {
                $created[] = self::create([
                    'campaign_id' => $campaignId,
                    'type'        => $type,
                    'status'      => 'pending',
                ]);
            }
        }

        return collect($created);
    }

    public function getStatusColorAttribute()
    {
        $colors = ['pending' => 'warning', 'verified' => 'success', 'rejected' => 'danger'];
        return $colors[$this->status] ?? 'secondary';
    }
}
