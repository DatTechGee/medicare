<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gift extends Model
{
    use HasFactory;

    protected $table = 'gifts';
    protected $fillable = ['title','amount','gifts','description','delivery_date','status','image','creator_id','creator_type'];

    public function cause(){
        return $this->belongsToMany(Cause::class);
    }
}
