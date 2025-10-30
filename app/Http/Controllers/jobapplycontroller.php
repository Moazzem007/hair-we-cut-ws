<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\personalinfoforjobmodel;
use App\Models\jobapplymodel;
use App\Models\partnercreatejob;

use Illuminate\Support\Facades\Auth;

class jobapplycontroller extends Controller
{

    public function index(){
        $check = personalinfoforjobmodel::where('job_creater',Auth::user()->id)->first();
        if($check  >= '1')
        {
            $show = partnercreatejob::with(['applied' => function ($query) {
                $query->where('application_id', Auth::user()->id);
            }])->get();
            return view('admin.barbar.jobs.jobs',compact('show','check'));
        }
        else{
        return view('admin.barbar.jobs.jobpersonalinfo');
        }
    }
    public function storeinfo(Request $req){
        $authid = Auth::user()->id;
        $show = personalinfoforjobmodel::where('job_creater',$authid)->first();
        if($show >='1'){
            return 'record is already exist';
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
    $store->save();
    return redirect()->route('infojobapply');
        }

    }

    public function editjobinfo(){
        $check = personalinfoforjobmodel::where('job_creater',Auth::user()->id)->first();
        return view('admin.barbar.jobs.editpersonalinfojob',compact('check'));
        
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
        $store->update();
        return redirect()->route('infojobapply')->with('status','Updation of personal information successfully');
    
        }


        public function getjobdata($id){
            $show = partnercreatejob::find($id);
            return response()->json(['show' => $show]);
        }

        public function jobapplynow(Request $req){
            $store = new jobapplymodel;
            $store->job_id = $req->jobid;
            $store->job_creater = $req->jobcreater;
            $store->application_id = Auth::user()->id;
            $store->save();
            return redirect()->back()->with('status', 'Job Apply Successfully');
    
        }
        public function getappliedjobs(){
        $authid = Auth::user()->id;
           
         $show = jobapplymodel::where('application_id',$authid)->with('job_info','apply_info','jobcreater')->get();
        return view('admin.barbar.jobs.appliedjob',compact('show'));
        }


    
}
