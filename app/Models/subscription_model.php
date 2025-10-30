<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class subscription_model extends Model
{
    use HasFactory;
    protected $table = 'subscription_package';
    protected $primerykey = 'id';

    protected $fillable = ['customer_id', 'product_id', 'service_id', 'subscription_type', 'quantity', 'each_amount', 'total_amount', 'duration', 'start_date', 'exp_date'];
}
