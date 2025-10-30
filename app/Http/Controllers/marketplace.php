<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\marketrentmodel;
use App\Models\marketproductsalemodel;
use App\Models\marketsalonmodel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;


class marketplace extends Controller
{
    public function marketrent(){
        $authid= Auth::user()->id;
        $show = marketrentmodel::where('job_creater',$authid)->get();
        return view('admin.barbar.marketplace.marketrentachair',compact('show'));
    }

    public function storerentchair(Request $req){
        $authid = Auth::user()->id;
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
    return redirect()->route('marketrentchair')->with('status','Insertion of Rents a Chairs successfully');
    }

    public function deletemarketrent($id){
        $result = marketrentmodel::find($id);
        $delete=public_path($result->image);
        //    dd($delete);
            if(File::exists($delete)){
                File::delete($delete);
               }
               $result->delete();
        return redirect()->route('marketrentchair')->with('status','Deletion of Rents a Chairs successfully');
    }

    public function editmarketrent($id){
        $show = marketrentmodel::find($id);
        return response()->json(['show' => $show]);
    }

    public function storeeditrentachair(Request $req){
    $store = marketrentmodel::find($req->id);
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
    $store->update();
    return redirect()->route('marketrentchair')->with('status','Updation of Rents a Chairs successfully');
    }


    // market place solen sell


    public function marketsalon(){
        $authid= Auth::user()->id;
        $show = marketsalonmodel::where('job_creater',$authid)->get();
        return view('admin.barbar.marketplace.marketsalonsell',compact('show'));
    }

    public function storesalon(Request $req){
        $authid = Auth::user()->id;
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
    $store->status = $req->status;
    $store->save();
    return redirect()->back()->with('status','Insertion of Business Salon successfully');

    }

    public function deletesalon($id){
        $result = marketsalonmodel::find($id);
        $delete=public_path($result->image);
        //    dd($delete);
            if(File::exists($delete)){
                File::delete($delete);
               }
               $result->delete();
        return redirect()->route('marketsalonsell')->with('status','Deletion of Business Salon successfully');
    }

    public function editmarketsalon($id){
        $show = marketsalonmodel::find($id);
        return response()->json(['show' => $show]);
    }

    public function editsalonsell(Request $req){
    $store = marketsalonmodel::find($req->id);
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
    $store->status = $req->status;
    $store->update();
    return redirect()->back()->with('status','Updation of Business Salon successfully');

    }


  // market place products

  public function marketproduct(){
    $authid= Auth::user()->id;
    $show = marketproductsalemodel::where('job_creater',$authid)->get();
    return view('admin.barbar.marketplace.marketproducts',compact('show'));
}

public function storeproducts(Request $req){
    $authid = Auth::user()->id;
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
    //     $file=$req->file('video');
    //       $filename=time().'.'.$file->getClientOriginalExtension();
    //       $store->video=$req->file('video')->move('public\video',$filename);
    //           }

          $store->specification = $req->specification;
    $store->status = $req->status;
    $store->save();
    return redirect()->back()->with('status','Insertion of Product successfully');
}


public function deleteproduct($id){
    $result = marketproductsalemodel::find($id);
    $delete=public_path($result->image);
    //    dd($delete);
        if(File::exists($delete)){
            File::delete($delete);
           }
           $result->delete();
    return redirect()->route('marketproducts')->with('status','Deletion of Product successfully');
}

public function editmarketproduct($id){
    $show = marketproductsalemodel::find($id);
    return response()->json(['show' => $show]);
}

public function editmarket(Request $req){
$store = marketproductsalemodel::find($req->id);
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
    //     $file=$req->file('video');
    //       $filename=time().'.'.$file->getClientOriginalExtension();
    //       $store->video=$req->file('video')->move('public\video',$filename);
    //           }

          $store->specification = $req->specification;
    $store->status = $req->status;
    $store->update();
    return redirect()->back()->with('status','Updation of Product successfully');
} 



// all market place listing

public function marketallproduct(){
    $show = marketproductsalemodel::orderBy('id','DESC')->get();
    return view('admin.barbar.marketplace.allmarketproducts',compact('show'));
}

// all market place busnisses
public function marketrentall(){
    $show = marketrentmodel::orderBy('id','DESC')->get();
    return view('admin.barbar.marketplace.allmarketrentachair',compact('show'));
}

// all market place salon
public function marketallsalon(){
    $show = marketsalonmodel::orderBy('id','DESC')->get();
    return view('admin.barbar.marketplace.allmarketsalonsell',compact('show'));
}


// view market place
    // view market place
    public function marketplace_productview($id)
    {
        $show = marketproductsalemodel::find($id);
        return view('admin.barbar.marketplace.marketplaceproductview',compact('show'));
    }

    public function marketplace_rentview($id)
    {
        $show = marketrentmodel::find($id);
        return view('admin.barbar.marketplace.marketplacerentview',compact('show'));
    }

    public function marketplace_salonview($id)
    {
        $show = marketsalonmodel::find($id);
        return view('admin.barbar.marketplace.marketplacesalonview',compact('show'));
    }

}
