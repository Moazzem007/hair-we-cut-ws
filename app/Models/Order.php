<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = [];


    public function soldproduct()
    {
        return $this->hasMany('App\Models\SoldProduct', 'order_id', 'id');
    }

    public function Customer()
    {
        return $this->belongsTo('App\Models\Customer', 'customer_id', 'id');
    }

    public function barber()
    {
        return $this->belongsTo(Barber::class,'barber_id','user_id');
    }
    

    
}
