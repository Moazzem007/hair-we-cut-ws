<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\productcartmodel;
use Illuminate\Support\Facades\Auth;



class productcartapi extends Controller
{
    public function storecart(Request $req){
        $store = new productcartmodel;
        $store->user_id = $req->user_id;
        $store->product_id = $req->product_id;
        $store->qty = $req->qty;
        $store->price = $req->price;
        $store->status = $req->status;
         $store->save();
         return response()->json(['status' => 'Add to cart is completed']);

    }

    
    
}
