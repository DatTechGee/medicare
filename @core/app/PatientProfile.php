<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PatientProfile extends Model
{
    protected $fillable = [
        'user_id', 'date_of_birth', 'gender', 'blood_group',
        'hospital_name', 'medical_condition', 'national_id', 'status'
    ];

    protected $dates = ['date_of_birth'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
