<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class marketsalonmodel extends Model
{
    use HasFactory;
    protected $table="marketsalonsell";
    protected $primarykey="id";
}
