<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayoutRequest extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function barber()
    {
        return $this->belongsTo(\App\Models\Barber::class, 'barber_id');
    }
}
