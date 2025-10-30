<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class product_rating_model extends Model
{
    use HasFactory;
    protected $table="product_rating";
    protected $primarykey="id";
}
