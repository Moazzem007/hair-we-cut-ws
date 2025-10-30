<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarberTimeSlot extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function appoitment()
    {
        return $this->hasMany('App\Models\Appointment','slote_id','id');
    }

    public function barber()
    {
        return $this->belongsTo('App\Models\Barber','slot_no','id')->orderBy('name','ASC');
    }



}
