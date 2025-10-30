<?php

namespace App\Http\Controllers;

use App\Models\Barber;
use App\Models\Category;
use App\Models\BarberProducts;
use App\Models\Product;
use App\Models\SoldProduct;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class BarberProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $assignsPro = BarberProducts::where([
            'pro_status' => 'Remove',
        ])->with('barber','category','product')->get();

        // return $assignsPro;
        return view('admin.product.assignbarberproducts',compact('assignsPro'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $categories = Category::all();
        $barbers = Barber::where(['status' => 'Active','is_business' => 1])->get();
        return view('admin.product.assignproduct',compact('categories','barbers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //

        try {
            
            $data = array(
                'barber_id'       => $request->barber,
                'cat_id'          => $request->category,
                'product_id'      => $request->product,
                'admin_quantity'  => 0,
                'barber_quantity' => $request->quantity,
                'pro_status'      => 'Remove',
                'status'          => 1,
            );

            $result = BarberProducts::create($data);

            if($result){
                return redirect()->route('barberproducts.index')->with('message','Product Assign Successull');
            }
            
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BarberProduct  $barberProduct
     * @return \Illuminate\Http\Response
     */
    public function show(BarberProduct $barberProduct)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BarberProduct  $barberProduct
     * @return \Illuminate\Http\Response
     */
    public function edit(BarberProduct $barberProduct)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BarberProduct  $barberProduct
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BarberProduct $barberProduct)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BarberProduct  $barberProduct
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //

        $id = BarberProducts::find($id);

        $result = $id->delete();

        if($result){
            return redirect()->route('barberproducts.index')->with('message','Product Assign Delete Successull');
        }
    }


    public function categoryProduct(Request $request){

        $data  = Product::where('cat_id','=',$request->cat_id)->get();
        return response()->json($data);
    }



    // BARBERS PRODUCTS
    public function barberproduct()
    {
        $userid = Auth::user()->id;
        $products = BarberProducts::where('barber_id',$userid)->with('product','category')->get();
        // return $products;

        return view('admin.barbar.barberproducts',compact('products'));
    }


    public function approve($id)
    {
        $row         = BarberProducts::find($id);
        $row->status = 2;
        $result      = $row->update();

        if($result){
            return redirect()->route('barberproduct')->with('message','Stock Approved');
        }
    }



    public function barberstock()
    {
        // $user = Auth::user();
        $userid = Auth::user()->id;

        $barberstocks = BarberProducts::where([
            'pro_status' => 'Remove',
            'barber_id' => $userid

        ])->with(['product.barberproduct'=> function($q){

            $q->where('status','=',2)->selectRaw('sum(barber_quantity) as stock, product_id')->groupBy('product_id');
             
        }])->with(['product.soldproduct'=> function($q) use($userid){

            $q->where('barber_id',$userid)->selectRaw('sum(quatity) as soldstock, product_id')->groupBy('product_id');
            
        }])->groupBy('product_id')->get();

        // return $barberstocks;
        return view('admin.barbar.barberstock',compact('barberstocks'));
    }
}
