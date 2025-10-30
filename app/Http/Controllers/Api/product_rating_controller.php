<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\product_rating_model;
use Illuminate\Support\Facades\Auth;

class product_rating_controller extends Controller
{
    public function store_product_rating(Request $request)
    {
        $store = new product_rating_model();
        $store->user_id = Auth::user()->id;
        $store->product_id = $request->product_id;
        $store->rating = $request->rating;
        $store->review = $request->review;
        $store->save();
        return response()->json(['status' => 'Product Rating Successfully Done']);
        
    }
}
