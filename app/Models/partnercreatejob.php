<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class partnercreatejob extends Model
{
    use HasFactory;

    protected $table = 'partnercreatejob';
    protected $primerykey = 'id';

    public function applied(){
        return $this->hasOne('App\Models\jobapplymodel','job_id','id');
    }

}
