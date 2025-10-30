<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    public function appointment()
    {
        return $this->hasMany('App\Models\Appointment', 'id', 'app_id');
    }

    public function barber()
    {
        return $this->belongsTo('App\Models\Barber','id');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer','user_id','id');
    }
   public function user_info()
    {
        return $this->belongsTo('App\Models\Customer','user_id','id');
    }
 public function salon_info()
    {
        return $this->hasMany('App\Models\Rating','salon_id','salon_id');
    }
}
