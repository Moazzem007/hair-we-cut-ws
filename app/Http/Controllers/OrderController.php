<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\ProductWallet;
use App\Models\Code;
use App\Models\Commission;
use App\Models\SoldProduct;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\CodeMail;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

        $user   = Auth::user()->id;
        $orders = Order::where('barber_id',$user)->with('customer')->get();
        // return $orders;
        return view('admin.barbar.orders.orders',compact('orders'));

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
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        try {

            $row         = Order::find($id);
            $row->status = 'Delivered';
            $run         = $row->update();

            if($run){
                return redirect()->route('adminproductorder');
            }

        } catch (\Exception $e) {
            $e->getMessage();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        //
    }



    
    // Product Payments
    public function productPayment(Request $request)
    {
        // return $request;
            
        try {
            $user = Auth::user();
            // Set your secret key. Remember to switch to your live secret key in production.
            // See your keys here: https://dashboard.stripe.com/apikeys
            \Stripe\Stripe::setApiKey('sk_test_51IUEm1Gp0YSIUkCJnomc7ayDjucczKUbUu6alcJNb2TteQ7jHV0PWk4CtE93AjiQmdcAZN2QxKIlS4G2odVnNIU300RiJ2suuU');

            // Token is created using Stripe Checkout or Elements!
            // Get the payment token ID submitted by the form:
            $token  = $request->token;

            $amount = $request->data['alldata']['amount'];

            $customer = \Stripe\Customer::create([
                'email'  => $user->email,
                'source' => $token,
            ]);

            
            $charge = \Stripe\Charge::create([
                'amount'      => $amount * 100,
                'customer'    => $customer->id,
                'currency'    => 'gbp',
                'description' => 'Products Payment',
            ]);
            
            if($request->data['alldata']['type'] == 'barber'){
                $barber    = 0;
                $com       = Commission::orderBy('id','desc')->first();
                $comission = @$com->product/100 * $amount;
            }else{
                $comission = 0;
                $barber    = 1;

            }
            $orderData = array(
                'type'        => $request->data['alldata']['type'],
                'contact'     => $request->data['alldata']['contact'],
                'address'     => $request->data['alldata']['address'],
                'customer_id' => $user->id,
            );
            
            $orders = Order::create($orderData);
            $mixid    = ProductWallet::max('inv');
            $inv      = $mixid + 1;    // Invoice
            
            if($orders){
                
                foreach ($request->data['alldata']['ids'] as $key => $proid){
                    $proData = array(
                        'product_id'  => $proid,
                        'quatity'     => $request->data['alldata']['qty'][$key],
                        'price'       => $request->data['alldata']['price'][$key],
                        'customer_id' => $user->id,
                        'order_id'    => $orders->id,
                        'barber_id'   => $barber,
                    );
                    
                    $soldPro = SoldProduct::create($proData);    
                }

               

                $paymentData = array(
                    'customer_id' => $user->id,
                    'barber_id'   => $barber,
                    'order_id'    => $orders->id,
                    'inv'         => $inv,
                    'debit'       => $amount,
                    'credit'      => 0,
                    'com_amount' => $comission,
                    'description' => 'Purchase Product Amount',
                );
    
                $payResult = ProductWallet::create($paymentData);
            }

            if($request->data['alldata']['type'] == 'barber'){
                $code = rand(0,9999);

                $codedata = array(
                    'email'    => $user->email,
                    'code'     => $code,
                    'order_id' => $orders->id,
                    'inv_id'   => $inv,
                );

                $result = Code::create($codedata);

                $data = array(
                    'email' => $user->email,
                    'code'  => $code,
                );
                Mail::to($user->email)->send(new CodeMail($data));

            }

                

            if($payResult){

                return response()->json([
                    'success' => true,
                    'Message' => 'Payment Successed'
                ]);

            }

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'Error' => $e->getMessage()
            ]);
        }
            
    }


    // Products Payent From Mobile 
    public function mobileproductPayment(Request $request)
    {
        try {

            $user = Auth::user();
            // Set your secret key. Remember to switch to your live secret key in production.
            // See your keys here: https://dashboard.stripe.com/apikeys
            \Stripe\Stripe::setApiKey('sk_test_51IUEm1Gp0YSIUkCJnomc7ayDjucczKUbUu6alcJNb2TteQ7jHV0PWk4CtE93AjiQmdcAZN2QxKIlS4G2odVnNIU300RiJ2suuU');

            // Token is created using Stripe Checkout or Elements!
            // Get the payment token ID submitted by the form:
            $token  = $request->token;

            $amount = $request->amount;

            $customer = \Stripe\Customer::create([
                'email'  => $user->email,
                'source' => $token,
            ]);

            
            $charge = \Stripe\Charge::create([
                'amount'      => $amount * 100,
                'customer'    => $customer->id,
                'currency'    => 'gbp',
                'description' => 'Products Payment',
            ]);
            
            if($request->type == 'barber'){
                $barber    = 0;
                $com       = Commission::orderBy('id','desc')->first();
                $comission = $com->product/100 * $amount;
            }else{
                $comission = 0;
                $barber    = 1;

            }
            $orderData = array(
                'type'        => $request->type,
                'contact'     => $request->contact,
                'address'     => $request->address,
                'customer_id' => $user->id,
            );
            
            $orders = Order::create($orderData);
            $mixid    = ProductWallet::max('inv');
            $inv      = $mixid + 1;    // Invoice
            
            if($orders){
                foreach ($request->ids as $key => $proid){
                    $proData = array(
                        'product_id'  => $proid,
                        'quatity'     => $request->qty[$key],
                        'price'       => $request->price[$key],
                        'customer_id' => $user->id,
                        'order_id'    => $orders->id,
                        'barber_id'   => $barber,
                    );
                    
                    $soldPro = SoldProduct::create($proData);    
                }

               

                $paymentData = array(
                    'customer_id' => $user->id,
                    'barber_id'   => $barber,
                    'order_id'    => $orders->id,
                    'inv'         => $inv,
                    'debit'       => $amount,
                    'credit'      => 0,
                    'com_amount'  => $comission,
                    'description' => 'Purchase Product Amount',
                );
    
                $payResult = ProductWallet::create($paymentData);
            }

            if($request->type == 'barber'){
                $code = rand(0,9999);

                $codedata = array(
                    'email'    => $user->email,
                    'code'     => $code,
                    'order_id' => $orders->id,
                    'inv_id'   => $inv,
                );

                $result = Code::create($codedata);

                $data = array(
                    'email' => $user->email,
                    'code'  => $code,
                );
                Mail::to($user->email)->send(new CodeMail($data));

            }

                

            if($payResult){

                return response()->json([
                    'success' => true,
                    'Message' => 'Payment Successed'
                ]);

            }

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'Error' => $e->getMessage()
            ]);
        }
            
        // try {

        //     $user = Auth::user();

        //     $amount = $request->amount;

        //     if($request->type == 'barber'){
        //         $barber    = 0;
        //         $com       = Commission::orderBy('id','desc')->first();
        //         $comission = $com->product/100 * $amount;
        //     }else{
        //         $comission = 0;
        //         $barber    = 1;
        //     }
            
        //     $orderData = array(
        //         'type'        => $request->type,
        //         'contact'     => $request->contact,
        //         'address'     => $request->address,
        //         'customer_id' => $user->id,
        //     );

        //     $orders = Order::create($orderData);
        //     $mixid    = ProductWallet::max('inv');
        //     $inv      = $mixid + 1;    // Invoice

        //     if($orders){
                
        //         foreach ($request->id as $key => $proid){
        //             $proData = array(
        //                 'product_id'  => $proid,
        //                 'quatity'     => $request->qty[$key],
        //                 'price'       => $request->price[$key],
        //                 'customer_id' => $user->id,
        //                 'order_id'    => $orders->id,
        //                 'barber_id'   => $barber,
        //             );

        //             $soldPro = SoldProduct::create($proData);


        //         }

                

        //         $paymentData = array(
        //             'customer_id' => $user->id,
        //             'barber_id'   => $barber,
        //             'order_id'    => $orders->id,
        //             'inv'         => $inv,
        //             'debit'       => $amount,
        //             'credit'      => 0,
        //             'com_amount' => $comission,
        //             'description' => 'Purchase Product Amount',
        //         );
    
        //         $payResult = ProductWallet::create($paymentData);
        //     }

        //     if($request->type == 'barber'){
                
        //         $code = rand(0,9999);
        //         $codedata = array(
        //             'email'    => $user->email,
        //             'code'     => $code,
        //             'order_id' => $orders->id,
        //             'inv_id'   => $inv,
        //         );

        //         $result = Code::create($codedata);

        //         $data = array(
        //             'email' => $user->email,
        //             'code'  => $code,
        //         );
        //         Mail::to($user->email)->send(new CodeMail($data));

        //     }

                

        //     if($payResult){

        //         return response()->json([
        //             'success' => true,
        //             'Message' => 'Payment Successed'
        //         ]);

        //     }

        // } catch (\Exception $e) {

        //     return response()->json([
        //         'success' => false,
        //         'Error' => $e->getMessage()
        //     ]);
        // }
            
    }


    public function adminproductorder()
    {
        $orders = Order::where('type','=','address')->with('customer')->orderBy('created_at','desc')->get();
        return view('admin.barbar.orders.productorder_admin',compact('orders'));
    }

    public function orderInvoice($id)
    {
        $order = Order::with('soldproduct.product')->find($id);
        return view('admin.barbar.orders.admin_invoice',compact('order'));
    }

    public function orderInvoiceViewToBarber($id)
    {
        $order = Order::with('soldproduct.product','customer')->find($id);
        // return $order;
        return view('admin.barbar.orders.invoice_view_barber',compact('order'));
    }

    public function barberproductorder()
    {
        $orders = Order::where('type','=','barber')->with('customer','barber')->orderBy('created_at','desc')->get();
        return view('admin.barbar.orders.productorder_barber',compact('orders'));
    }


    
}
