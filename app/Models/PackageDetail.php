<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageDetail extends Model
{
    use HasFactory;

    protected $guarded = [];


    public function package()
    {
        return $this->belongsTo(Package::class,'package_id');
    }


    public function  service()
    {
        return $this->belongsTo(Service::class,'service_id');
    }

    public function  product()
    {
        return $this->belongsTo(Product::class,'product_id')->withDefault(['name'=>'']);
    }
}
