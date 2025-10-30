<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\subscription_model;
use Illuminate\Http\Request;

class subscriptioncontroller extends Controller
{
    public function subscription(Request $request){
       
        $data =array();
         $customer_ids = $request->customer_id;
         $product_ids = $request->product_id;
         $service_ids = $request->service_id;
         $subscription_types = $request->subscription_type;
         $quantitys = $request->quantity;
         $each_amounts = $request->each_amount;
         $total_amounts = $request->total_amount;
         $durations = $request->duration;
         $start_dates = $request->start_date;
         $exp_dates = $request->exp_date;
        foreach ($customer_ids as $key => $customers) {
            $data[]=[
                "customer_id"       => $customers,
                "product_id"        => @$product_ids[$key],
                "service_id"        => @$service_ids[$key],
                "subscription_type" => @$subscription_types[$key],
                "quantity"          => @$quantitys[$key],
                "each_amount"       => @$each_amounts[$key],
                "total_amount"      => @$total_amounts[$key],
                "duration"          => @$durations[$key],
                "start_date"         => @$start_dates[$key],
                "exp_date"          => @$exp_dates[$key]
            ];
        }
        subscription_model::create($data);
   
       return response()->json(['status' => 'Subscription Package Successfully Subscribe']); 
    }
}
