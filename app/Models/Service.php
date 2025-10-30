<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;


    // Protected 
    protected $guarded = [];

    public function appoitment()
    {
        return $this->hasMany('App\Models\Appointment','service_id','id');
    }


    public function  packagedetail()
    {
        return $this->hasMany(PackageDetail::class,'service_id');
    }
}
