<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\marketrentmodel;
use App\Models\marketproductsalemodel;
use App\Models\marketsalonmodel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;




class marketplacecontroller extends Controller
{
    public function storerent(Request $req){
        $authid = Auth::user()->user_id;
    $store = new marketrentmodel;
    $store->job_creater = $authid;
    $store->business = $req->business;
    $store->category = $req->category;
    $store->address = $req->address;
    $store->chairs = $req->chairs;
    $store->price = $req->price;
    $store->description = $req->description;
    $store->availablefrom = $req->availablefrom;
    if($req->hasFile('image')){
    $file=$req->file('image');
    $filename=time().'.'.$file->getClientOriginalExtension();
    $store->image=$req->file('image')->move('public/images',$filename);
    }
    $store->contactname = $req->contactname;
    $store->contactnumbar = $req->contactnumbar;
    $store->contactemail = $req->contactemail;
    $store->status = $req->status;
    $store->save();
    return response()->json(['status' => 'insertion of market rent chair is successfully']);

    }

    public function getmarketrent(){
        $authid = Auth::user()->user_id;
        $data['marketrent'] = marketrentmodel::where('job_creater',$authid)->get();
        foreach ($data['marketrent'] as &$item) {
            $item->image = asset($item->image);
        }
        return response()->json($data);
    }
    
    public function getmarketrentdetails($id){
        $data['marketrent'] = marketrentmodel::find($id);
        return response()->json($data);
    }

    public function getallmarketrent(){
        $authid = Auth::user()->user_id;
        $data['allmarketrent'] = marketrentmodel::where('job_creater', '!=', $authid)->get();
        foreach ($data['allmarketrent'] as &$item) {
            $item->image = asset($item->image);
        }
        return response()->json($data);
    }

 public function deletemarketrent($id){
        $result = marketrentmodel::find($id);
        $delete=public_path($result->image);
        //    dd($delete);
            if(File::exists($delete)){
                File::delete($delete);
               }
               $result->delete();
               return response()->json(['status' => 'Deletion Successfully']);
    }

    //  MARKET PLACE FOR SALON SALL
    public function storesalonsell(Request $req){
        $authid = Auth::user()->user_id;
    $store = new marketsalonmodel;
    $store->job_creater = $authid;
    $store->salon_name = $req->salon_name;
    $store->address = $req->address;
    $store->barber_available = $req->barber_available;
    $store->price = $req->price;
    $store->description = $req->description;
    if($req->hasFile('image')){
    $file=$req->file('image');
    $filename=time().'.'.$file->getClientOriginalExtension();
    $store->image=$req->file('image')->move('public/images',$filename);
    }
    $store->contactname = $req->contactname;
    $store->contactnumbar = $req->contactnumbar;
    $store->contactemail = $req->contactemail;
    $store->status = $req->status;
    $store->save();
    return response()->json(['status' => 'insertion of market salon sales is successfully']);

    }

    public function getmarketsalon(){
        $authid = Auth::user()->user_id;
        $data['marketsalonsale'] = marketsalonmodel::where('job_creater',$authid)->get();
        foreach ($data['marketsalonsale'] as &$item) {
            $item->image = asset($item->image);
        }
        return response()->json($data);
    }
    
    public function getmarketsalonsaledetails($id){
        $data['marketsalonsale'] = marketsalonmodel::find($id);
        return response()->json($data);
    }

    public function getmarketsalonall(){
        $authid = Auth::user()->user_id;
        $data['getmarketsalonall'] = marketsalonmodel::where('job_creater','!=',$authid)->get();
        foreach ($data['getmarketsalonall'] as &$item) {
            $item->image = asset($item->image);
        }
        return response()->json($data);
    }

 public function deletesalon($id){
        $result = marketsalonmodel::find($id);
        $delete=public_path($result->image);
        //    dd($delete);
            if(File::exists($delete)){
                File::delete($delete);
               }
               $result->delete();
               return response()->json(['status' => 'Deletion Successfully']);

    }

    // for market product 
    public function storeproduct(Request $req){
        $authid = Auth::user()->user_id;
        $store = new marketproductsalemodel;
        $store->job_creater = $authid;
        $store->product_name = $req->product_name;
        $store->category = $req->category;
        $store->brand = $req->brand;
        $store->price = $req->price;
        $store->discountprice = $req->discountprice;
        $store->shift_cost = $req->shift_cost;
        $store->short_description = $req->short_description;
        $store->detail_description = $req->detail_description;
        if($req->hasFile('image')){
            $file=$req->file('image');
            $filename=time().'.'.$file->getClientOriginalExtension();
            $store->image=$req->file('image')->move('public/images',$filename);
            }

        // if($req->hasFile('video')){
        //   $file=$req->file('video');
        //     $filename=time().'.'.$file->getClientOriginalExtension();
        //     $store->video=$req->file('video')->move('public/images',$filename);
        //         }

            $store->specification = $req->specification;
            $store->contactname = $req->contactname;
            $store->contactnumbar = $req->contactnumbar;
            $store->contactemail = $req->contactemail;
            $store->status = $req->status;
            $store->save();
    return response()->json(['status' => 'insertion of market place product  is successfully']);

    }

    public function getmarketproduct(){
        $authid = Auth::user()->user_id;
        $data['marketproducts'] = marketproductsalemodel::where('job_creater',$authid)->get();
        foreach ($data['marketproducts'] as &$item) {
            $item->image = asset($item->image);
        }
        return response()->json($data);
    }
    
    public function getmarketproductdetails($id){
        $data['marketproduct'] = marketproductsalemodel::find($id);
        return response()->json($data);
    }

    public function getmarketproductsall(){
        $authid = Auth::user()->user_id;
        $data['marketproductsall'] = marketproductsalemodel::where('job_creater','!=',$authid)->get();
        foreach ($data['marketproductsall'] as &$item) {
            $item->image = asset($item->image);
        }
        return response()->json($data);
    }
        public function deleteproduct($id){
        $result = marketproductsalemodel::find($id);
        $delete=public_path($result->image);
        //    dd($delete);
            if(File::exists($delete)){
                File::delete($delete);
               }
               $result->delete();
               return response()->json(['Status' => 'Deletion Successfully']);
    }

// out side market place api 
public function getallmarketrents(){
    $data['all rents chairs'] = marketrentmodel::get();
    foreach ($data['all rents chairs'] as &$item) {
        $item->image = asset($item->image);
    }
    return response()->json($data);
}

public function getallmarketsalon(){
    $data['all salon'] = marketsalonmodel::get();
    foreach ($data['all salon'] as &$item) {
        $item->image = asset($item->image);
    }
    return response()->json($data);
}

public function getallmarketproduct(){
    $data['all products'] = marketproductsalemodel::get();
    foreach ($data['all products'] as &$item) {
        $item->image = asset($item->image);
    }
    return response()->json($data);
}

}
