<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Appointment;
use App\Models\Wallet;
use App\Models\Order;
use App\Models\Rating;
use App\Models\SoldProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Service;
use App\Models\Commission;
use App\Models\AppointmentLog;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\UserSignUp;
use Illuminate\Support\Facades\Log;
use Stripe; 



class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
 
        $user = Auth::user();
		   $user->img = asset($user->img);
        $useremail = Auth::user()->email;

        return response()->json(['user' =>$user, 'useremail' => $useremail]);
    }

    public function tokenupdate(Request $request)
    {
        try {

            // $role = [
                    
            //     'token'     => 'required',
            // ];

            // $validateData = Validator::make($request->all(),$role);

            // if($validateData->fails()){

            //     return response()->json([
            //         'message' => 'Invalid data send',
            //         'Error' => $validateData->errors(),
            //     ], 400);

            // }

            $user = Auth::user();

            if($request->token == null){
                $user->device_token = null;
                $user->update();
                $result = 'Token Removed';
                return response()->json([
                    'success' => true,
                    'message'   => $result,
                ]);
            }

            if($user->device_token != $request->token){
                $user->device_token = $request->token;
                $user->update();
                $result = 'New Token';
            }else{
                $result = 'Old Token';
            }

            return response()->json([
                'success' => true,
                'message'   => $result,
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'Erorr'   => $e->getMessage(),
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

    public function signup(Request $request)
    {
        $role = [
            
            'name'     => 'required|min:3',
            'email'    => 'email|required|unique:customers,email',
            'contact'  => 'required',
            'password' => 'required|min:8|confirmed',

        ];

        $validateData = Validator::make($request->all(),$role);

        if($validateData->fails()){

            return response()->json([
                'message' => 'Invalid data send',
                'Error'   => $validateData->errors(),
            ],400);
        }
        try {
            

            if($request->token){
                $token = $request->token;
            }else{
                $token = '';
            }

            $data = array(
                'name'         => $request->name,
                'email'        => $request->email,
                'password'     => bcrypt($request->password),
                'contact'      => $request->contact,
                'device_token' => $token,
            );

            $result = Customer::create($data);

            $dataMail = array(
                'name'     => $request->name,
                'contact'  => $request->contact,
                'email'    => $request->email,
                'password' => $request->password,
                'otp'      => $request->otp ? $request->otp : 0,
            );

            if ($result){
                try {
                    Mail::to($request->email)->send(new UserSignUp($dataMail));
                    Log::info('User sign-up mail sent successfully to user: ' . $request->email);
                } catch (\Exception $e) {
                    Log::error('Failed to send user sign-up mail to user: ' . $request->email . ' Error: ' . $e->getMessage());
                }
                return response()->json([
                    'success' => true,
                    'Message' => "Customer Register",
                ]);
            }


       } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'Erorr'   => $e->getMessage(),
            ]);

        }
    }

    // Profile Update
    public function profileUpdate(Request $request)
    {
        
        try {

            $row = Customer::find($request->id);

            if ($request->has('billing_address')) {
                $row->billing_address = $request->billing_address;
            }
            if ($request->has('postal_code')) {
                $row->postal_code = $request->postal_code;
            }

        if ($request->hasFile('image')) {

                $file=$request->file('image');
                $filename=time().'.'.$file->getClientOriginalExtension();
                $row->img=$request->file('image')->move('public/images',$filename);
            }
            $row->name = $request->name;
            $result    = $row->update();


            if ($result) {

               return  response()->json([
                   'success' => true,
                   'Message' => 'Profile updated successfully',
               ]);

            }
            
            

        } catch (\Exception $e) {

            return  response()->json([
                'success' => false,
                'Error'   => $e->getMessage(),
            ]);
        }
    }


    public function request_change_password(Request $request)
    {
        $role = [
            
            'email'    => 'email|required',

        ];

        $validateData = Validator::make($request->all(),$role);

        if($validateData->fails()){

            return response()->json([
                'message' => 'Invalid data send',
                'Error'   => $validateData->errors(),
            ],400);
        }

        try {
            $customer = Customer::where('email','=',$request->email)->first();

            if ($customer) {

                return  response()->json([
                    'success' => true,
                    'id'      => $customer->id,
                    'Message' => 'Valid User',
                ]);
                
            }else{
                return  response()->json([
                    'success' => false,
                    'id'      => 0,
                    'Message' => 'Invalid User',
                ]);
             }

            
        } catch (\Exception $e) {

            return  response()->json([
                'success' => false,
                'Error'   => $e->getMessage(),
            ]);
        }
    }


    public function request_update_password(Request $request)
    {
        $role = [
            
            'id'       => 'required',
            'password' => 'required|min:8|confirmed',
        ];

        $validateData = Validator::make($request->all(),$role);

        if($validateData->fails()){

            return response()->json([
                'message' => 'Invalid data send',
                'Error'   => $validateData->errors(),
            ],400);
        }

        try {

            $customer           = Customer::find($request->id);
            $customer->password = bcrypt($request->password);
            $result             = $customer->update();

            if ( $result) {

                return  response()->json([
                    'success' => true,
                    'Message' => 'Password Changed Successfully..!',
                ]);
                
            }

            
        } catch (\Exception $e) {

            return  response()->json([
                'success' => false,
                'Error'   => $e->getMessage(),
            ]);
        }
    }

    public function customerappoinments()
    {
        $userid = Auth::user()->id;

        // Total Appointment
        $appointmentspending  = Appointment::where('customer_id',$userid)->where('status','Pendding')->with('barber','salon','service','slot','rating')->orderBy('id','desc')->get();
        $appointmentscom  = Appointment::where('customer_id',$userid)->where('status','!=','Pendding')->with('barber','salon','service','slot','rating')->orderBy('id','desc')->get();
        $total_app     = Appointment::where('customer_id',$userid)->count();
        $completed_app = Appointment::whereIn('status',['Completed','Review'])->where('customer_id', $userid)->count();

        $canceled_app = Appointment::where([
            'customer_id' => $userid,
            'status'      => 'Canceled'
        ])->count();

        $order = Order::where('customer_id',$userid)->with('soldproduct.product.category')->get();
        // $rating = Rating::where('user_id',$userid)->with('barber')->get();
       
        return response()->json([
            'appointmentspending' => $appointmentspending,
            'appointmentscom' => $appointmentscom,

            'totalapp'     => $total_app,
            'completed'    => $completed_app,
            'canceled'     => $canceled_app,
            'order'        => $order,
            // 'reviewlist'   => $rating,
        ]);
    }


    public function cusorders()
    {
        $userid = Auth::user()->id;

        $order = Order::where('customer_id',$userid)->with('soldproduct.product.category')->get();
        // $rating = Rating::where('user_id',$userid)->with('barber')->get();
       
        return response()->json([
            
            'order'        => $order,
            // 'reviewlist'   => $rating,
        ]);
    }

    public function customerorders()
    {
        $userid = Auth::user()->id;


        $orderpending = Order::where('customer_id',$userid)->where('status','Pending')->with('soldproduct.product')->get();
        $ordercom = Order::where('customer_id',$userid)->where('status','!=','Pending')->with('soldproduct.product')->get();
    
       
        return response()->json([
            'orderpending'        => $orderpending,
            'ordercom'        => $ordercom,

          
        ]);
    }
	

    public function customerordercancl($id){
        $ordercan = Order::find($id);
        $ordercan->status = 'Canceled';
        $ordercan->update();
        return response()->json([
            'status'        => 'Order Canceled Succesfuly'
          
        ]);
    }

    public function orderdetail($id)
    {
        $data = soldproduct::where('order_id',$id)->with('product')->get();
        return response()->json($data);
        
    }


    public function serviceamount($id)
    {
        $data = Appointment::with('service')->find($id);
        $com  = Commission::orderBy('id','desc')->first();
        return response()->json([
            'amount' => $data->service->price,
            'com'    => @$com->percent,
        ]);
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
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //

        try {
            $row       = Customer::find($id);
            $row->name = $request->name;
            $result    = $row->update();
            
           if($result){
                return response()->json([
                    'success' => true,
                    'Message' => "Customer Updated",
                ]);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'Erorr'   => $e->getMessage(),
            ]);

        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        //
    }


    // PAYMENT

    public function orderPost(Request $request)
    {
            
        try {
            

            $user = Auth::user();
            // Set your secret key. Remember to switch to your live secret key in production.
            // See your keys here: https://dashboard.stripe.com/apikeys
            \Stripe\Stripe::setApiKey('sk_test_51IUEm1Gp0YSIUkCJnomc7ayDjucczKUbUu6alcJNb2TteQ7jHV0PWk4CtE93AjiQmdcAZN2QxKIlS4G2odVnNIU300RiJ2suuU');

            // Token is created using Stripe Checkout or Elements!
            // Get the payment token ID submitted by the form:
            $token  = $request->token;

            $appid  = $request->data['id'];
            $amount = $request->data['amount'];

            $customer = \Stripe\Customer::create([
                'email'  => $user->email,
                'source' => $token,
            ]);

            $charge = \Stripe\Charge::create([
                'amount'      => $amount * 100,
                'customer'    => $customer->id,
                'currency'    => 'gbp',
                'description' => 'Appointment Amount',
            ]);        

            $appdata = Appointment::with('service')->find($appid);

            $mixid       = Wallet::max('inv');
            $inv         = $mixid + 1;                                 // Invoice
            $com         = $amount - $appdata->service->price;
            $paymentData = array(
                'user_id'        => $user->id,
                'barber_id'      => $appdata->barber_id,
                'salon_id'       => $appdata->salon_id,
                'appointment_id' => $appid,
                'inv'            => $inv,
                'debit'          => $appdata->service->price,
                'credit'         => 0,
                'com_amount'     => $com,
                'description'    => 'Appointment Booking Payment',
            );

            $payResult = Wallet::create($paymentData);

            if ($payResult) {
                
                $row               = Appointment::find($appid);
                $row->status       = "Paid";
                $row->amount       = $amount;
                $row->stripe_token = $charge->id;
                $result            = $row->update();
            }

            if($result){

                $log = [
                    'appointment_id' => $appid,
                    'status'         => "PAID",
                    'payment'        => $amount,
                ];
                AppointmentLog::create($log);

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

    // For APis Payment From Mobile Side
    public function orderPostfromapi(Request $request)
    {
            
        try {
            

            $user = Auth::user();

            $appid = $request->data['id'];
            $amount = $request->data['amount'];

            $appdata = Appointment::find($appid);

            $mixid    = Wallet::max('inv');
            $inv      = $mixid + 1;    // Invoice

            $paymentData = array(

                'user_id'        => $user->id,
                'barber_id'      => $appdata->barber_id,
                'salon_id'       => $appdata->salon_id,
                'appointment_id' => $appid,
                'inv'            => $inv,
                'debit'          => $amount,
                'credit'         => 0,
                'description'    => 'Appointment Booking Payment',
                
            );

            $payResult = Wallet::create($paymentData);

            if ($payResult) {
                
                $row         = Appointment::find($appid);
                $row->status = "Paid";
                $result      = $row->update();
            }

            if($result){

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


    public function pricing()
    {
        try {
            
            $service = Service::where('user_id','=',0)->get();
            return response()->json($service);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'Error' => $e->getMessage()
            ]);
        }
       
    }
  
}
