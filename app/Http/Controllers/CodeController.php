<?php

namespace App\Http\Controllers;

use App\Models\Code;
use App\Models\Order;
use App\Models\ProductWallet;
use App\Models\SoldProduct;
use App\Models\BarberCommission;
use App\Models\Commission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class CodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('admin.barbar.orders.code');
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
            'email' => 'required',
            'code' => 'required|max:4',
        );

        $validator = Validator::make($request->all(),$rule);
        if ($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput();
        }



        try {

           if($data = Code::where(['email' => $request->email, 'code' => $request->code])->first()  ){
                $products = SoldProduct::where('order_id',$data->order_id)->with('customer','product')->get();
                $order    = Order::find($data->order_id);
                return view('admin.barbar.orders.invoice',get_defined_vars());
            }else{
            
            return back()->with('Error','Code Can not be Match');

           }

        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Code  $code
     * @return \Illuminate\Http\Response
     */
    public function show($id, $order)
    {
        //
        try {

            $user   = Auth::user()->id;
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
                $result3              = Code::where('order_id',$order)->delete();
                $row_order            = Order::find($order);
                $row_order->barber_id = Auth::user()->id;
                $row_order->status          = 'Delivered';
                $row_order->update();
                return redirect()->route('orders.index');
            }
            

        } catch (\Exception $e) {
            $e->getMessage();
        }

        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Code  $code
     * @return \Illuminate\Http\Response
     */
    public function edit(Code $code)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Code  $code
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Code $code)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Code  $code
     * @return \Illuminate\Http\Response
     */
    public function destroy(Code $code)
    {
        //
    }
}
