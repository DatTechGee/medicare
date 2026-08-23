<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FraudReport extends Model
{
    protected $table = 'fraud_reports';

    protected $fillable = [
        'campaign_id', 'fraud_score', 'risk_level', 'status',
        'check_results', 'evidence', 'recommendation', 'admin_notes', 'reviewed_by',
    ];

    protected $casts = [
        'check_results' => 'array',
        'evidence' => 'array',
    ];

    public function campaign()
    {
        return $this->belongsTo(Cause::class, 'campaign_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(Admin::class, 'reviewed_by');
    }

    public function getScoreColorAttribute()
    {
        if ($this->fraud_score <= 20) return 'success';
        if ($this->fraud_score <= 50) return 'warning';
        return 'danger';
    }

    public function getRiskBadgeAttribute()
    {
        $colors = ['low' => 'success', 'medium' => 'warning', 'high' => 'danger'];
        $color = $colors[$this->risk_level] ?? 'secondary';
        return '<span class="badge badge-' . $color . '">' . ucfirst($this->risk_level) . '</span>';
    }
}
