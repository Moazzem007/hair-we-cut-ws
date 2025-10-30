<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\partnercreatejob;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;



class partnerjobcreatecontroller extends Controller
{
    public function store(Request $req){
        $create_id = Auth::user()->user_id;
        $store = new partnercreatejob;
        $store->job_creater = $create_id;
        $store->title = $req->title;
        $store->companyname = $req->companyname;
        $store->email = $req->email;
        $store->contactno = $req->contact;
        $store->experience = $req->experience;
        $store->salary = $req->salary;
        $store->gate_no = $req->gateno;
        $store->city = $req->city;
        $store->gender = $req->gender;
        $store->employee_type = $req->employee;
        $store->role = $req->role;
        $store->vacancies = $req->vacancies;
        $store->job_description = $req->description;
        $store->status = $req->status;
        $store->save();

        return response()->json(['status' => 'insertion job creation completed']);

    }
    public function getalljobs($id){
        $alljobs['all_jobs'] = partnercreatejob::where('job_creater',$id)->get();
        return response()->json($alljobs);
    }

    public function updatecreatedjob(Request $req){
        $create_id = Auth::user()->user_id;
        $store = partnercreatejob::find($req->id);
        $store->job_creater = $create_id;
        $store->title = $req->title;
        $store->companyname = $req->companyname;
        $store->email = $req->email;
        $store->contactno = $req->contact;
        $store->experience = $req->experience;
        $store->salary = $req->salary;
        $store->gate_no = $req->gateno;
        $store->city = $req->city;
        $store->gender = $req->gender;
        $store->employee_type = $req->employee;
        $store->role = $req->role;
        $store->vacancies = $req->vacancies;
        $store->job_description = $req->description;
        $store->status = $req->status;
        $store->update();

        return response()->json(['status' => 'updation job completed']);

    }

    public function getdata(){
        $showdata['jobs'] = partnercreatejob::with(['applied' => function ($query) {
            $query->where('application_id', Auth::user()->user_id);
        }])->get();
        return response()->json($showdata);
    }
    public function viewjobdetails($id){
        $jobdetails = partnercreatejob::find($id);
        return response()->json($jobdetails);
    }

    public function deletejob($id){
        $alljobs['delete_jobs'] = partnercreatejob::find($id)->delete();
        return response()->json(['status' => 'creater job deletion sucessfully']);
    }


    public function getalljoboutside(){
        $data['all jobs'] = partnercreatejob::get();
        return response()->json($data);
    }
}
