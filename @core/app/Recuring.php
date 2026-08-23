<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recuring extends Model
{
    use HasFactory;

    protected $table = 'recurings';
    protected $fillable = ['cause_log_id','status','expire_date'];

    public function donation_log()
    {
        return $this->belongsTo(CauseLogs::class, 'cause_log_id', 'id');
    }
}
