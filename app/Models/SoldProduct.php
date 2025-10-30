<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoldProduct extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer', 'customer_id','id');
    }

    public function barber()
    {
        return $this->hasMany('App\Models\Barber', 'user_id', 'barber_id');
    }

    public function order()
    {
        return $this->belongsTo('App\Models\Order', 'id', 'order_id');
    }
}
