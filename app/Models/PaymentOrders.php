<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentOrders extends Model
{
    use HasFactory;

    protected $fillable = ['reference','amount','currency','status','opayo_transaction_id','opayo_response'];
    protected $casts = ['opayo_response' => 'array'];
}
