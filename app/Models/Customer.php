<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Customer extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $guarded = [];
    protected $hidden = ['email','password'];
    
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

    //appointment wallet
    public function wallet()
    {
        return $this->hasMany(Wallet::class,'user_id','id');
    }

    // product wallet
    public function productwallet()
    {
        return $this->hasMany(ProductWallet::class,'customer_id','id');
    }

    public function soldproduct()
    {
        return $this->hasMany('App\Models\SoldProduct', 'id', 'customer_id');
    }

    public function order()
    {
        return $this->hasMany('App\Models\Order', 'id', 'customer_id');
    }

    public function appoitment()
    {
        return $this->hasMany('App\Models\Appointment','customer_id','id');
    }

    public function rating()
    {
        return $this->hasMany('App\Models\Rating','user_id');
    }
}
