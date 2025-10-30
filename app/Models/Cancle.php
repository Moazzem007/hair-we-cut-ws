<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cancle extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function appointment()
    {
        return $this->hasMany('App\Models\Appointment', 'id', 'appointment_id');
    }
}
