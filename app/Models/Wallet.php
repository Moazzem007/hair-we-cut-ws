<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;


    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(Customer::class,'user_id','id');
    }

    public function barber()
    {
        return $this->belongsTo(Barber::class,'barber_id','user_id');
    }

    public function appointment()
    {
        return $this->hasMany('App\Models\Appointment', 'id', 'appointment_id');
    }
}
