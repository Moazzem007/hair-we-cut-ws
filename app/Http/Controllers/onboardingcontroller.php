<?php

namespace App\Http\Controllers;
use App\Models\onboardingstartmodel;
use App\Models\onboardinghomemodel;
use Illuminate\Support\Facades\File;

use Illuminate\Http\Request;

class onboardingcontroller extends Controller
{
    public function index(){
        $images = onboardingstartmodel::get();
        return view('admin/onboarding/homescreen', compact('images'));
    }

    public function startscreenimage(Request $req){
      $store = new onboardingstartmodel;
      if($req->hasFile('image')){
        $file=$req->file('image');
        $filename=time().'.'.$file->getClientOriginalExtension();
        $store->image=$req->file('image')->move('public/images',$filename);
        }
        $store->save();
        return redirect()->back();
    }

    public function deletestartimage($id){
        $result = onboardingstartmodel::find($id);
        $delete=public_path($result->image);
  
            if(File::exists($delete)){
                File::delete($delete);
               }
               $result->delete();
        return redirect()->back()->with('status','Start image Deleted successfully');
    }

    public function homeimage(){
        $images = onboardinghomemodel::get();
        return view('admin/onboarding/screen', compact('images'));
    }

    public function homescreenimage(Request $req){
      $store = new onboardinghomemodel;
      if($req->hasFile('image')){
        $file=$req->file('image');
        $filename=time().'.'.$file->getClientOriginalExtension();
        $store->image=$req->file('image')->move('public/images',$filename);
        }
        $store->save();
        return redirect()->back();
    }

    public function deletehomeimage($id){
        $result = onboardinghomemodel::find($id);
        $delete=public_path($result->image);
  
            if(File::exists($delete)){
                File::delete($delete);
               }
               $result->delete();
        return redirect()->back()->with('status','Home image Deleted successfully');
    }

    // apis 

    public function get_startimages(){
        $data= onboardingstartmodel::get();
        foreach ($data as &$item) {
            $item->image = asset($item->image);
        }
        return response()->json($data);
    }

    public function get_homeimages(){
        $data= onboardinghomemodel::get();
        foreach ($data as &$item) {
            $item->image = asset($item->image);
        }
        return response()->json($data);
    }
}
