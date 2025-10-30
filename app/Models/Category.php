<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    public function proudct()
    {
        return $this->hasMany('App\Models\Product','cat_id');
    }

    public function barberproduct()
    {
        return $this->hasMany('App\Models\BarberProduct','cat_id');
    }
}
