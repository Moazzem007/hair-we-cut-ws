<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Package;
use App\Models\Service;
use App\Models\PackageDetail;
use Carbon\Carbon;

class PackageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

        try {

            $packages = Package::where("customer_id",2)->with('detail.service','detail.product')->get();

            return response()->json([
                'success' => true,
                'data' => $packages,
            ]);
            
        }  catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'Error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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

        $role = [
                    
            // 'date'     => 'required',
            'type'     => 'required',
            'amount'   => 'required',
            'discount' => 'required',
            'services' => 'required|array',
            'products' => 'required|array',
        ];

        $validateData = Validator::make($request->all(),$role);

        if($validateData->fails()){

            return response()->json([
                'message' => 'Invalid data send',
                'Error' => $validateData->errors(),
            ], 400);

        }

       try {
            $package = Package::create([
                "date"         => Carbon::now(),
                "exp_date"     => $request->type == "Weekly" ?  Carbon::now()->addDays(7) : Carbon::now()->addDays(30),
                "discount"     => $request->discount,
                "package_type" => $request->type,
                "amount"        => $request->amount,
                "customer_id"  => Auth::user()->id,
            ]);
            
            if($package){
                foreach ($request->services as $value) {
                    $run = PackageDetail::create([
                        "package_id" => $package->id,
                        "service_id" => $value['id'],
                        "price"      => $value['price'],
                    ]);
                }
                
                foreach ($request->products as $product) {
                    $run2 = PackageDetail::create([
                        "package_id" => $package->id,
                        "product_id" => $product['id'],
                        "price"      => $product['price'],
                    ]);
                }
            }

            if($run){
                return response()->json([
                    'success' => true,
                    "message" => "Package Added Successfull"
                ]);
            }
       } catch (\Exception $e) {

        return response()->json([
            'success' => false,
            'Error' => $e->getMessage()
        ]);
    }


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    function package_services(){
        try {
            $services = Service::where('user_id',1)->get();
            return response()->json([
                'success' => true,
                'data' => $services,
            ]);
        }  catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'Error' => $e->getMessage()
            ]);
        }
    }
}
