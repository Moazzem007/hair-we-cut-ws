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
use Intervention\Image\ImageManagerStatic as Image;


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

    // List Partner's Own Products
    public function partner_products_list()
    {
        $userid = Auth::user()->user_id;
        $products = Product::where('barber_id', $userid)->with('category')->get();

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    // Add Partner's Own Product
    public function add_partner_product(Request $request)
    {
        $rule = array(
            'category'  => 'required',
            'currency'  => 'required',
            'type'      => 'required',
            'product'   => 'required',
            'price'     => 'required',
            'saleprice' => 'required',
            'image'     => 'required|image',
            'slug'      => 'required',
            'quantity'  => 'required|integer|min:0',
        );

        $validator = Validator::make($request->all(), $rule);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid data',
                'errors' => $validator->errors(),
            ], 400);
        }

        try {
            $userid = Auth::user()->user_id;

            $row = new Product();
            if ($request->type == 4) {
                $row->dprice  = $request->dprice;
                $row->percent = $request->percent;
            } else {
                $row->dprice  = 0;
                $row->percent = 0;
            }

            $row->product_name = $request->product;
            $row->slug         = $request->slug;
            $row->cat_id       = $request->category;
            $row->price        = $request->price;
            $row->currency     = $request->currency;
            $row->type         = $request->type;
            $row->sale_price   = $request->saleprice;
            $row->barber_id    = $userid;

            $image     = $request->file('image');
            $extension = $image->getClientOriginalExtension();
            $filename  = time() . '.' . $extension;

            $image_resize = Image::make($image->getRealPath());
            $image_resize->resize(250, 250);
            $image_resize->save(public_path('products/' . $filename));
            $row->img = $filename;

            $result = $row->save();

            if ($result) {
                // Assign initial stock to the partner
                BarberProducts::create([
                    'barber_id'       => $userid,
                    'cat_id'          => $request->category,
                    'product_id'      => $row->id,
                    'admin_quantity'  => 0, // Admin gave 0
                    'barber_quantity' => $request->quantity, // Partner stock
                    'pro_status'      => 'Add',
                    'status'          => 2, // 2 Means approved / assigned
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Product Added Successfully',
                    'data' => $row
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Update Partner's Own Product
    public function update_partner_product(Request $request, $id)
    {
        try {
            $userid = Auth::user()->user_id;
            $row = Product::where('barber_id', $userid)->findOrFail($id);

            if ($request->has('category')) $row->cat_id = $request->category;
            if ($request->has('slug')) $row->slug = $request->slug;
            if ($request->has('product')) $row->product_name = $request->product;
            if ($request->has('price')) $row->price = $request->price;
            if ($request->has('currency')) $row->currency = $request->currency;
            if ($request->has('type')) $row->type = $request->type;
            if ($request->has('saleprice')) $row->sale_price = $request->saleprice;
            
            if ($request->has('dprice') && $request->type == 4) $row->dprice = $request->dprice;
            if ($request->has('percent') && $request->type == 4) $row->percent = $request->percent;

            if ($request->hasFile('image')) {
                $image        = $request->file('image');
                $extension    = $image->getClientOriginalExtension();
                $filename     = time() . '.' . $extension;
                $image_resize = Image::make($image->getRealPath());
                $image_resize->resize(250, 250);
                $image_resize->save(public_path('products/' . $filename));
                $row->img = $filename;
            }

            $result = $row->update();

            if ($result) {
                // Update specific assigned stock if quantity provided
                if ($request->has('quantity')) {
                    $barberProduct = BarberProducts::where('barber_id', $userid)
                        ->where('product_id', $row->id)
                        ->first();
                        
                    if ($barberProduct) {
                        $barberProduct->barber_quantity = $request->quantity;
                        $barberProduct->update();
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Product Updated Successfully',
                    'data' => $row
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Delete Partner's Own Product
    public function delete_partner_product($id)
    {
        try {
            $userid = Auth::user()->user_id;
            $product = Product::where('barber_id', $userid)->findOrFail($id);
            
            // Clean up related assignments
            BarberProducts::where('product_id', $id)->delete();
            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product Deleted Successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
