<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer','customer_id','id');
    }

    public function barber()
    {
        return $this->belongsTo('App\Models\Barber','barber_id','id');
    }
	    public function salon()
    {
        return $this->belongsTo('App\Models\Barber','salon_id','id');
    }

    public function service()
    {
        return $this->belongsTo('App\Models\Service','service_id','id');
    }

    public function slot()
    {
        return $this->belongsTo('App\Models\BarberTimeSlot','slote_id','id');
    }

    public function wallet()
    {
        return $this->belongsTo('App\Models\Wallet', 'id', 'appointment_id');
    }

    public function rating()
    {
        return $this->belongsTo('App\Models\Rating', 'id', 'app_id');
    }

    public function reason()
    {
        return $this->belongsTo('App\Models\Cancle', 'id', 'appointment_id');
    }

    public function log()
    {
        return $this->hasMany('App\Models\AppointmentLog','appointment_id','id');
    }

    
}
