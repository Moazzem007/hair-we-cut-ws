<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class jobapplymodel extends Model
{
    use HasFactory;
    protected $table="jobapplyprovider";
    protected $primarykey="id";

    public function apply_info(){
        return $this->belongsTo('App\Models\personalinfoforjobmodel','application_id','job_creater');
    }
    public function jobcreater(){
        return $this->belongsTo('App\Models\Barber','job_creater','user_id');

    }

    public function job_info(){
        return $this->belongsTo('App\Models\partnercreatejob','job_id','id');
    }
}
