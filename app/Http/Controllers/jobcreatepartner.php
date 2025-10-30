<?php

namespace App\Http\Controllers;
use App\Models\partnercreatejob;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class jobcreatepartner extends Controller
{
    public function index(){
        $authid= Auth::user()->id;
        $show = partnercreatejob::where('job_creater',$authid)->get();
        return view('admin.barbar.jobs.partnercreatejob',compact('show'));
    }

    public function storepartnerjob(Request $req){
        $create_id = Auth::user()->id;
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
        $store->save();
        return redirect()->route('partnercreatejob');
    }
        // view jobs
        public function job_view($id)
        {
            $show = partnercreatejob::find($id);
            return view('admin.barbar.jobs.jobview',compact('show'));
        }
}
