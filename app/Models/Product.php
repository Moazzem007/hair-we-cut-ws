<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public function category()
    {
        return $this->belongsTo('App\Models\Category','cat_id','id');
    }

    public function barberproduct()
    {
        return $this->hasMany('App\Models\BarberProducts','product_id','id');
    }

    public function productwallet()
    {
        return $this->hasMany('App\Models\ProductWallet','product_id','id');
    }

    public function soldproduct()
    {
        return $this->hasMany('App\Models\SoldProduct', 'product_id', 'id');
    }


    public function  packagedetail()
    {
        return $this->hasMany(PackageDetail::class,'product_id');
    }




}
