<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Barber extends Authenticatable implements JWTSubject
{
    use HasFactory;

    protected $guarded = [];
    protected $hidden = ['password'];


    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

     /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    // appointment wallet
    public function wallet()
    {
        return $this->hasMany(Wallet::class,'barber_id','user_id');
    }

    // product wallet
    public function productwallet()
    {
        return $this->hasMany(ProductWallet::class,'barber_id','user_id');
    }

    public function appoitment()
    {
        return $this->hasMany('App\Models\Appointment','barber_id','id');
    }


    public function rating()
    {
        return $this->hasMany('App\Models\Rating','barber_id');
    }
	
	   public function salonrating()
    {
        return $this->hasMany('App\Models\Rating','salon_id');
    }


    public function soldproduct()
    {
        return $this->belongsTo('App\Models\SoldProduct', 'user_id', 'customer_id');
    }

    public function order()
    {
        return $this->hasMany(Order::class,'user_id','barber_id');
    }


    public function slot()
    {
        return $this->hasMany('App\Models\BarberTimeSlot','slot_no');
    }
	
	    public function service()
    {
        return $this->hasMany('App\Models\Service','user_id','barber_of');
    }
}
