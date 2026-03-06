<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppointmentReschedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'barber_id',
        'proposed_slote_id',
        'proposed_date',
        'status',
        'message',
        'requested_by',
        'handled_by',
        'handled_at',
    ];

    protected $dates = [
        'proposed_date',
        'handled_at',
        'created_at',
        'updated_at',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function barber()
    {
        return $this->belongsTo(Barber::class, 'barber_id');
    }

    public function proposedSlot()
    {
        return $this->belongsTo(BarberTimeSlot::class, 'proposed_slote_id');
    }
}
