<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;
use App\CauseLogs;
use App\Admin;
use App\DonationCategory;

class Cause extends Model
{
    protected $table = 'causes';
    protected $fillable = ['cause_update_id','title','cause_content','amount','raised','status','slug','image','meta_title',
        'image_gallery','meta_tags','meta_description','user_id','admin_id','deadline','faq','created_by','featured','categories_id',
        'excerpt','og_meta_title','og_meta_description','og_meta_image','medical_document','emmergency','reward','gift_status','monthly_donation_status',
        'fraud_score','verification_status','hospital_name','patient_name','medical_details','wallet_address','wallet_verified','wallet_verified_at',
        'document_hash','document_hashed_at','document_verified_at'];

    protected $casts = [
        'deadline' => 'date',
        'wallet_verified_at' => 'datetime',
        'document_hashed_at' => 'datetime',
        'document_verified_at' => 'datetime',
    ];

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
    public function cause_logs(){
        return $this->belongsTo(CauseLogs::class,'id','cause_id');
    }
    public function admin(){
        return $this->belongsTo(Admin::class,'admin_id');
    }
    public function category(){
        return $this->belongsTo(CauseCategory::class,'categories_id');
    }
    public function cause_update(){
        return $this->belongsTo(CauseUpdate::class,'cause_update_id');
    }

    public function cause_updates_data(){
        return $this->hasMany(CauseUpdate::class,'cause_id','id');
    }

    public function comments(){
        return $this->hasMany(Comment::class,'cause_id','id');
    }

    public function donors(){
        return $this->hasMany(Cause::class);
    }

    public function withdraws(){
        return $this->hasMany(DonationWithdraw::class,'donation_id');
    }

    public function gift(){
        return $this->belongsToMany(Gift::class);
    }

    public function blockchain_transactions(){
        return $this->hasMany(BlockchainTransaction::class, 'campaign_id');
    }

    public function fraud_reports(){
        return $this->hasMany(FraudReport::class, 'campaign_id');
    }

    public function verifications(){
        return $this->hasMany(Verification::class, 'campaign_id');
    }

    public function wallets(){
        return $this->hasMany(Wallet::class, 'campaign_id');
    }

    public function getProgressPercentageAttribute()
    {
        return $this->amount > 0 ? round(($this->raised / $this->amount) * 100) : 0;
    }

    public function getVerificationStatusColorAttribute()
    {
        $colors = ['unverified' => 'secondary', 'pending' => 'warning', 'verified' => 'success', 'rejected' => 'danger'];
        return $colors[$this->verification_status] ?? 'secondary';
    }

}
