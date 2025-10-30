<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductWallet extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo('App\Models\Product','product_id','id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class,'customer_id','id');
    }

    public function barber()
    {
        return $this->belongsTo(Barber::class,'barber_id','user_id');
    }

    public function code()
    {
        return $this->hasOne('App\Models\Code','inv_id','inv');
    }
}
