<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MobileSlider extends Model
{
    use HasFactory;

    protected $table = 'mobile_sliders';
    protected $casts = ['campaign_id' => 'integer'];
    protected $fillable = ['title','image','subtitle','donation_id'];
}
