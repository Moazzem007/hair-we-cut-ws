<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class productcartmodel extends Model
{
    use HasFactory;
    protected $table = 'productcart';
    protected $primerykey = 'id';
}
