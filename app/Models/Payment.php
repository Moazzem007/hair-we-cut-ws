<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'transaction_type',
        'vendor_tx_code',
        'transaction_id',
        'status',
        'amount',
        'currency',
        'requires_3ds',
        'acs_url',
        'three_ds_data',
        'raw_request',
        'raw_response',
    ];

    protected $casts = [
        'three_ds_data' => 'array',
        'raw_request'   => 'array',
        'raw_response'  => 'array',
        'requires_3ds'  => 'boolean',
    ];

    public function order()
    {
        return $this->belongsTo(PaymentOrders::class);
    }
}
