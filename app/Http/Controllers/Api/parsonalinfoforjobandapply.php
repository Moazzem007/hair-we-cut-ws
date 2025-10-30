<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\personalinfoforjobmodel;
use App\Models\jobapplymodel;

use Illuminate\Support\Facades\Auth;

class parsonalinfoforjobandapply extends Controller
{
    public function storeinfo(Request $req){
        $authid = Auth::user()->user_id;
        $show = personalinfoforjobmodel::where('job_creater',$authid)->first();
        if($show >='1'){
            return response()->json(['status' => 'personal information for job is already exist']); 
        }
        else{
    $store = new personalinfoforjobmodel;
    $store->job_creater = $authid;
    $store->name = $req->name;
    $store->contect = $req->contact;
    $store->email = $req->email;
    $store->gateno = $req->gateno;
    $store->city = $req->city;
    $store->postal_code = $req->postal_code;
    $store->experiencebarber = $req->experiencebarber;
    $store->previewsalonname = $req->previewsalonname;
    $store->presalonaddress = $req->presalonaddress;
    $store->fromdate = $req->fromdate;
    $store->todate = $req->todate;
    $store->position_role = $req->position_role;
    $store->reasonforleaving = $req->reasonforleaving;
    $store->barber_licence_no = $req->barber_licence_no;
    $store->institute_name = $req->institute_name;
    $store->institute_address = $req->institute_address;
    $store->certificate_training = $req->certificate_training;
    $store->skill = $req->skill;
    $store->available = $req->available;
    $store->status = $req->status;

    $store->save();
    return response()->json(['status' => 'insertion of personal information for job is successfully']);
        }

    }


    public function getinfo($id){
        $data = personalinfoforjobmodel::where('job_creater',$id)->first();
        return response()->json($data);
    }


    public function storeinfoupdate(Request $req){
    $store = personalinfoforjobmodel::find($req->id);
    $store->name = $req->name;
    $store->contect = $req->contact;
    $store->email = $req->email;
    $store->gateno = $req->gateno;
    $store->city = $req->city;
    $store->postal_code = $req->postal_code;
    $store->experiencebarber = $req->experiencebarber;
    $store->previewsalonname = $req->previewsalonname;
    $store->presalonaddress = $req->presalonaddress;
    $store->fromdate = $req->fromdate;
    $store->todate = $req->todate;
    $store->position_role = $req->position_role;
    $store->reasonforleaving = $req->reasonforleaving;
    $store->barber_licence_no = $req->barber_licence_no;
    $store->institute_name = $req->institute_name;
    $store->institute_address = $req->institute_address;
    $store->certificate_training = $req->certificate_training;
    $store->skill = $req->skill;
    $store->available = $req->available;
    $store->status = $req->status;
    $store->update();
    return response()->json(['status' => 'updation of personal information for job is successfully']);

    }

    public function jobapply(Request $req){
        $authid = Auth::user()->user_id;

        $store = new jobapplymodel;
        $store->job_id = $req->job_id;
        $store->job_creater = $req->job_creater;
        $store->application_id = $authid;
        $store->save();
        return response()->json(['status' => 'Job Apply Completed']);

    }

    public function getapplication($id){
        $data['applications'] = jobapplymodel::where('job_creater',$id)->with('job_info','apply_info','jobcreater')->get();
        return response()->json($data);
    }

    public function getappliedjobs(){
        $authid = Auth::user()->user_id;
           
         $data = jobapplymodel::where('application_id',$authid)->with('job_info','apply_info','jobcreater')->get();
        
        return response()->json($data);
        }


}
