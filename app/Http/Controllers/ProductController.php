<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use App\Models\Product;
use App\Models\BarberProducts;
use Illuminate\Http\Request;
use Intervention\Image\ImageManagerStatic as Image;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $products= Product::with('category')->get();
        // return $products;
        return view('admin.product.index',compact('products'));
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
        return view('admin.product.create',compact('categories'));
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
        $rule = array(
            'category'  => 'required',
            'currency'  => 'required',
            'type'      => 'required',
            'product'   => 'required',
            'price'     => 'required',
            'saleprice' => 'required',
            'image'     => 'required',
            'slug'      => 'required',
        );

        $validator = Validator::make($request->all(),$rule);
        if ($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput();
        }


        try {

            $row = new Product();

            if($request->type==4){

                $row->dprice  = $request->dprice;
                $row->percent = $request->percent;
            }
            else{
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


            $image     = $request->file('image');
            $extension = $image->getClientOriginalExtension();
            $filename  = time() . '.'.$extension;

            $image_resize = Image::make($image->getRealPath());
            $image_resize->resize(250,250);
            $image_resize->save(public_path('products/'.$filename));
            $row->img = $filename;


            $result = $row->save();

            if($result){
                return redirect()->route('adminproducts.index')->with('message','Product Added Successful');
            }

        } catch (\Exception $e) {
            
            return $e->getMessage();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //
        $product = Product::join('categories','categories.id','=','products.cat_id')
        ->select('products.*','products.id','categories.category_name','categories.id as catid')->find($id);

        $categories = Category::all();
        return view('admin.Products.product_edit',compact('product','categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //
        try {
            $row = Product::findOrFail($id);
        } catch (\Exception $e) {
                return $e->getMessage();
        }
        
        if($request->hasfile('image')){

            $row->cat_id       = $request->category;
            $row->slug         = $request->slug;
            $row->product_name = $request->product;
            $row->price        = $request->price;
            $row->quantity     = $request->quantity;
            $row->currency     = $request->currency;

            $image        = $request->file('image');
            $extension    = $image->getClientOriginalExtension();
            $filename     = time() . '.'.$extension;
            $image_resize = Image::make($image->getRealPath());
            $image_resize->resize(990,590);
            $image_resize->save(public_path('products/'.$filename));

            $row->img = $filename;

            $result = $row->update();
     
        }else{

            $row->cat_id       = $request->category;
            $row->slug         = $request->slug;
            $row->product_name = $request->product;
            $row->price        = $request->price;
            $row->quantity     = $request->quantity;
            $row->currency     = $request->currency;
            
            $result = $row->update();
        }
        if($result){
            return redirect()->route('adminproducts.index')->with('flash_succes','Product Update Success');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //

        $id = Product::find($id);
        $result = $id->delete();

        if($result){
            return redirect()->route('adminproducts.index')->with('message','Product Delete Successfull');
        }
    }



    // STOCK FUNCTIONS

    
    public function stocklist()
    {
        //
        $assignsPro = BarberProducts::with('category','product')->get();
        // return $assignsPro;
        return view('admin.product.stocklist',compact('assignsPro'));
    }



    public function stock()
    {
        //
        $categories = Category::all();
        return view('admin.product.addstoke',compact('categories'));
    }

    public function stockstore(Request $request)
    {
        // dd($request->all());

        try {
            
            $data = array(

                'barber_id'       => 0,
                'cat_id'          => $request->category,
                'product_id'      => $request->product,
                'admin_quantity'  => $request->quantity,
                'barber_quantity' => 0,
                'pro_status'      => 'Add',
                'status'          => 0,
            );

            $result = BarberProducts::create($data);

            if($result){
                return redirect()->route('stocklist')->with('message','Product Assign Successull');
            }
            
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function stockdelete($id)
    {
        //

        $id = BarberProducts::find($id);

        $result = $id->delete();

        if($result){
            return redirect()->route('stocklist')->with('message','Product Assign Delete Successull');
        }
    }



    public function currentstock()
    {
        //

        $stocks = Product::with(['barberproduct'=> function($q){
                $q->selectRaw('sum(admin_quantity) - sum(barber_quantity) as stock, product_id')->groupBy('product_id');
        }])->with('category')->get();       

        return view('admin.product.stock',compact('stocks'));
    }


    public function productsforapi()
    {
        try {
            
            $products = Product::with('category')->get();
            return response()->json($products);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'Error' => $e->getMessage()
            ]);
        }
       
    }

    public function searchproducts(Request $request)
    {
        try {
            
            $products = Product::where('product_name','LIKE','%'.$request->search.'%')->get();
               $data = ['products'=>$products];
            return response()->json($data);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'Error' => $e->getMessage()
            ]);
        }
       
    }


}
 