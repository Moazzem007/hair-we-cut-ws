<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Barber;
use App\Models\Category;
use App\Models\BarberProducts;
use App\Models\Product;
use App\Models\SoldProduct;
use App\Models\Code;
use App\Models\Order;
use App\Models\ProductWallet;


class BarberProductApiController extends Controller
{
    //

    // Barber Products Details
    public function barberproduct()
    {
        $userid   = Auth::user()->user_id;
        $products = BarberProducts::where('barber_id',$userid)->with('product','category')->get();

        return  response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    // Code Verification
    public function codeverifcation(Request $request)
    {
        $rule = array(
            'email' => 'required',
            'code'  => 'required|max:4',
        );
    
        $validateData = Validator::make($request->all(),$rule);

        if($validateData->fails()){

            return response()->json([
                'message' => 'Invalid data send',
                'Error' => $validateData->errors(),
            ], 400);

        }
    
    
    
        try {
    
           if($data = Code::where(['email' => $request->email, 'code' => $request->code])->first()){
                $products = SoldProduct::where('order_id',$data->order_id)->with('customer','product')->get();
                $order    = Order::find($data->order_id);
                

                return response()->json([
                    'success'  => true,
                    'codedata' => $data,
                    'product'  => $products,
                    'order'    => $order,
                ]);



            }else{
            
                return response()->json([
                    'success' => false,
                    'Erorr'   => "Code Can Not Match",
                ]);
    
           }
    
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'Erorr'   => $e->getMessage(),
            ]);
        }
    }



    // Deliver Product
    public function productdeliver($id, $order)
    {
        //
        try {

            $user = Auth::user()->user_id;


            $result = ProductWallet::where('inv',$id)->update([
                'barber_id' => $user,
            ]);

            if($result){

                $rows = SoldProduct::where('order_id',$order)->get();

                foreach($rows as $row){
                   $findRow        = SoldProduct::find($row->id);
                   $row->barber_id = $user;
                   $row->status    = 'Delivered';
                   $result2        = $row->update();
                }
            }

            if($result2){
                $result3 = Code::where('order_id',$order)->delete();
            }
            
            if($result3){
                return response()->json([
                    'success' => true,
                    'Message'   => "Product Delivered",
                ]);
            }

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'Erorr'   => $e->getMessage(),
            ]);
            
        }

        
    }

    // Barber Current Stock
    public function barberstock()
    {
       
        $userid = Auth::user()->user_id;

        $barberstocks = BarberProducts::where([
            'pro_status' => 'Remove',
            'barber_id' => $userid

        ])->with(['product.barberproduct'=> function($q){

            $q->where('status','=',2)->selectRaw('sum(barber_quantity) as stock, product_id')->groupBy('product_id');
             
        }])->with(['product.soldproduct'=> function($q) use($userid){

            $q->where('barber_id',$userid)->selectRaw('sum(quatity) as soldstock, product_id')->groupBy('product_id');
            
        }])->groupBy('product_id')->get();


        return response()->json([
            'success' => true,
            'data'    => $barberstocks,
        ]);
    }


    // Barber Orders
    public function orders()
    {
        $user = Auth::user()->user_id;
        $orders = SoldProduct::where('barber_id',$user)->with('product','customer')->get();

        return response()->json([
            'success' => true,
            'data'    => $orders,
        ]);
    }

    // Barber Aproval Of Stock
    public function stockapprove($id)
    {
       try {
            $row         = BarberProducts::find($id);
            $row->status = 2;
            $result      = $row->update();
            if($result){
                return response()->json([
                    'success' => true,
                    'Message'   => "Product Approved",
                ]);
            }

       } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'Erorr'   => $e->getMessage(),
            ]);
            
        }

       
    }
}
