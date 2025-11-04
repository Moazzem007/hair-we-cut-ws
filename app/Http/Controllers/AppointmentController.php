<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\BarberTimeSlot;
use App\Models\User;
use App\Models\Barber;
use App\Models\Cancle;
use App\Models\Wallet;
use App\Models\AppointmentLog;
use App\Models\ProductWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\AppointmentMail;
use App\Mail\RefundPayment;
use App\Http\Controllers\FcmController;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public $fcmController;

    public function __construct()
    {
        $this->fcmController = new FcmController();
    }
    public function index()
    {
        //
        $appointments = Appointment::with('customer','service','slot','barber','rating','log')->orderBy('created_at','desc')->get();
        // return $appointments;
        return view('admin.appointment.index',compact('appointments'));
    }


    public function barberAppointment()
    {
        $userid = Auth::user()->id;

        // Total Appointment
        $appointments = Appointment::where('salon_id',$userid)->with('customer','barber','service','slot','reason','rating')->orderBy('created_at','desc')->get();
        // return $appointments;
        return view('admin.barbar.appointments',compact('appointments'));
    }


    public function completedstatus($id)
    {
        $row = Appointment::find($id);

        $row->status = 'Completed';
        $result = $row->update();
        if ($result) {
            return redirect()->route('barberAppointment');
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
        try {

        $role = [
            'time'      => 'required',
            'barber_id' => 'required',
            'type'      => 'required',
            'service'   => 'required',
            'address'   => $request->appType == 'Mobile_shop' ? 'required' :'',
            'slote'     => 'required',
            'lat'       => $request->appType == 'Mobile_shop' ? 'required' :'',
            'lng'       => $request->appType == 'Mobile_shop' ? 'required' :'',
            'appType'   => 'required',
            'town'    =>$request->appType == 'Mobile_shop' ? 'required' :'',
            'postcode'=>$request->appType == 'Mobile_shop' ? 'required' :'',
            'address2'=>$request->appType == 'Mobile_shop' ? 'required' :'',
        ];

            $validateData = Validator::make($request->all(),$role);

            if($validateData->fails()){

                return response()->json([
                    'message' => 'Invalid data send',
                    'Error' => $validateData->errors(),
                ], 400);
            }
            $barber = Barber::find($request->barber_id);

            $data = array(
                'date'         => $request->time,
                'barber_id'    => $request->barber_id,
                'service_type' => $request->type,
                'address'      => $request->address,
                'address2' => $request->address2 ?? '',
                'town'     => $request->town ?? '',
                'postcode' => $request->postcode ?? '',
                'service_id'   => $request->service,
                'slote_id'     => $request->slote,
                'lat'          => $request->lat,
                'lng'          => $request->lng,
                'appType'      => $request->appType,
                'customer_id'  => Auth::user()->id,
                'salon_id'     => $barber->barber_of
            );

            $result = Appointment::create($data);

            $usermaildata = array(
                'name'    => Auth::user()->name,
                'date'    => $request->time,
                'contact' => Auth::user()->contact,
                'time'    => BarberTimeSlot::find($request->slote),
                'email'   => Auth::user()->email,
                'barber'     => $barber->name,
                'salon'     => $barber->barber_of,
                'appType' => $request->appType,
            );

            if ($result) {
            // Mail::to(Auth::user()->email)->send(new AppointmentMail($usermaildata));

            if(Auth::user()->device_token != null){
                $this->fcmController->sendNotification(new \Illuminate\Http\Request([
                    'token' => Auth::user()->device_token,
                    'title' => 'New Appointment',
                    'body' => 'Your appointment request is being processed.',
                    'email' => Auth::user()->email,
                ]));
            }

                $barber = Barber::find($request->barber_id);
               $user = User::find($barber->barber_of);

               if($user->device_token != null){
                    $this->fcmController->sendNotification(new \Illuminate\Http\Request([
                        'token' => $user->device_token,
                        'title' => 'New Appointment',
                        'body' => 'You have a new appointment request.',
                        'email' => $user->email,
                    ]));
                }

                $log = [
                    'appointment_id' => $result->id,
                    'status'         => "ADD",
                    'payment'        => 0,
                ];
                AppointmentLog::create($log);

                return response()->json([
                    'success' => true,
                    'app_id'  => $result->id,
                ]);
            }

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'Erorr'   => $e->getMessage(),
            ]);
        }
    }



    // Appointment Notifications

    public function notification($token)
    {

        try {

            $SERVER_API_KEY = 'AAAA3HaTkiY:APA91bH7w0D8dBQDGLith9YEOMwbW6y-uUPabDzaDp8uos84uIDIAryeWUU9o3d7KdczvjlC-8GrqCZcIpT1Qj_j1mjP-DmGXFSkbfthAp2ZDKBG6QtQ2B3zVLvDBKwnH6ANfnwau3fL';
            // $token = ;
            $data = [
                "registration_ids" =>   array (
                    $token
                ),
                "notification" => [
                    "title" => "Appointment Notification",
                    "body" => 'Your appointment has been booked with us',
                ]
            ];
            $dataString = json_encode($data);

            $headers = [
                'Authorization: key='.$SERVER_API_KEY,
                'Content-Type: application/json',
            ];

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

            $response = curl_exec($ch);

            return response()->json([
                'success' => true,
                'message' => $response,
            ]);




        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'Error' => $e->getMessage(),
            ]);
        }
    }

    public function barbernotification($token)
    {

        try {

            $SERVER_API_KEY = 'AAAA3HaTkiY:APA91bH7w0D8dBQDGLith9YEOMwbW6y-uUPabDzaDp8uos84uIDIAryeWUU9o3d7KdczvjlC-8GrqCZcIpT1Qj_j1mjP-DmGXFSkbfthAp2ZDKBG6QtQ2B3zVLvDBKwnH6ANfnwau3fL';
            // $token = ;
            $data = [
                "registration_ids" =>   array (
                    $token
                ),
                "notification" => [
                    "title" => "Appointment Notification",
                    "body" => 'You have New Appointment',
                ]
            ];
            $dataString = json_encode($data);

            $headers = [
                'Authorization: key='.$SERVER_API_KEY,
                'Content-Type: application/json',
            ];

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

            $response = curl_exec($ch);

            return response()->json([
                'success' => true,
                'message' => $response,
            ]);




        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'Error' => $e->getMessage(),
            ]);
        }
    }



    public function sendNotification($token, $title, $body)
    {

        try {

            $SERVER_API_KEY = 'AAAA3HaTkiY:APA91bH7w0D8dBQDGLith9YEOMwbW6y-uUPabDzaDp8uos84uIDIAryeWUU9o3d7KdczvjlC-8GrqCZcIpT1Qj_j1mjP-DmGXFSkbfthAp2ZDKBG6QtQ2B3zVLvDBKwnH6ANfnwau3fL';
            // $token = ;
            $data = [
                "registration_ids" =>   array (
                    $token
                ),
                "notification" => [
                    "title" => $title,
                    "body" => $body,
                ]
            ];
            $dataString = json_encode($data);

            $headers = [
                'Authorization: key='.$SERVER_API_KEY,
                'Content-Type: application/json',
            ];

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

            $response = curl_exec($ch);

            return response()->json([
                'success' => true,
                'message' => $response,
            ]);




        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'Error' => $e->getMessage(),
            ]);
        }
    }



    public function appointmentajax(Request $request)
    {
        $role = [
            'time'      => 'required',
            'slote'     => 'required'

        ];

        $validateData = Validator::make($request->all(),$role);

        if($validateData->fails()){

            return response()->json([
                'message' => 'Invalid data send',
                'Error' => $validateData->errors(),
            ], 400);
        }

        // return $appdate;
        $app = Appointment::where([
            'date' => $request->time,
            'slote_id' => $request->slote,

        ])->count();

        if ($app > 0) {
            return response()->json([
                'Message' => 'Slot Is Booked',
            ]);
        }else{
            return response()->json([

                'Message' => 'Slot Is Avaliable',
            ]);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //

        try {
            $app         = Appointment::find($id);

            return response()->json($app);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'Error' => $e->getMessage()
            ]);

        }
    }


    public function notificationappointment()
    {
        $authid = Auth::user()->id;

        try {
            $app         = Appointment::where('customer_id',$authid)->with('slot','customer','barber')->orderby('id','desc')->get();
            $products    =ProductWallet::where('customer_id',$authid)->with('code')->orderby('id','desc')->get();
			$data = array_merge($app->toArray(), $products->toArray());
            usort($data, function($a, $b) {
             return $b['id'] - $a['id'];
              });
            return response()->json($data);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'Error' => $e->getMessage()
            ]);

        }
    }


	    public function updateappointment(Request $request){
        try {
        $store = Appointment::find($request->id);
        $store->view = $request->view;
        $store->update();
        return response()->json(['status' =>'appointment updated']);

    } catch (\Exception $e) {

        return response()->json([
            'success' => false,
            'Error' => $e->getMessage()
        ]);

    }
    }
	public function countnotification()
{
    $authid = Auth::user()->id;

    try {
            $app         = Appointment::where('customer_id',$authid)->where('view','unview')->get();
            $products    =ProductWallet::where('customer_id',$authid)->where('view','unview')->get();
		$count_app = $app->count();
		$count_product = $products->count();
		$count_notification = $count_app+$count_product;
        return response()->json($count_notification);

    } catch (\Exception $e) {

        return response()->json([
            'success' => false,
            'Error' => $e->getMessage()
        ]);

    }
}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function edit(Appointment $appointment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Appointment $appointment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Appointment $appointment)
    {
        //
    }


    public function cancleapp(Request $request)
    {

        try {
            // return $request;

            $data = [
                'barber_id'      => $request->barber_id,
                'user_id'        => $request->user_id,
                'appointment_id' => $request->id,
                'reason'         => $request->reason,
            ];
            $run = Cancle::create($data);



            if($run){

                $app                 = Appointment::find($request->id);
                $app->status         = 'Canceled';
                $app->cancel_payment = $request->cancelpayment;
                $app->update();

                $log = [
                    'appointment_id' => $request->id,
                    'status'         => "CANCELED",
                    'payment'        => 0,
                ];
                AppointmentLog::create($log);

                return response()->json([
                    'success' => true,
                    'Message' => "Appointment Cancled"
                ]);
            }


        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'Error' => $e->getMessage()
            ]);

        }

    }

    public function cancleApi($id)
    {

        try {
            // return $request;
            $app         = Appointment::find($id);
            $app->status = 'Canceled';
            $app->update();

            return response()->json([
                'success' => true,
                'Message' => "Appointment Cancled"
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'Error' => $e->getMessage()
            ]);

        }

    }

    public function refundPayment($id)
    {

        $app  = Appointment::with('wallet','customer')->find($id);
        $user = Auth::user();
        // Set your secret key. Remember to switch to your live secret key in production.
        // See your keys here: https://dashboard.stripe.com/apikeys
        \Stripe\Stripe::setApiKey('sk_test_51IUEm1Gp0YSIUkCJnomc7ayDjucczKUbUu6alcJNb2TteQ7jHV0PWk4CtE93AjiQmdcAZN2QxKIlS4G2odVnNIU300RiJ2suuU');

        // Token is created using Stripe Checkout or Elements!
        // Get the payment token ID submitted by the form:
        // $token  = $app->stripe_token;
        $token  = $app->stripe_token;
        $logStatus = '';
        if($app->cancel_payment){
            $amount    = ($app->wallet->debit + $app->wallet->com_amount) - 6;
            $logStatus = "PARTIAL-REFUND";
        }else{
            $logStatus = "TOTAL-REFUND";
            $amount    = ($app->wallet->debit + $app->wallet->com_amount);
        }


       $refund =  \Stripe\Refund::create([
            'charge'   => $token,
            'amount'   => $amount * 100,
        ]);




        Wallet::where('appointment_id',$id)->delete();
        $mixid  = Wallet::max('inv');
        $inv    = $mixid + 1;

        if($app->cancel_payment){
            $paymentData = array(
                'user_id'        => $app->customer_id,
                'barber_id'      => $app->barber_id,
                'appointment_id' => $id,
                'inv'            => $inv,
                'debit'          => 6,
                'credit'         => 0,
                'com_amount'     => 0,
                'description'    => 'Cancellation Fee',
            );

        }else{
            $paymentData = array(
                'user_id'        => $app->customer_id,
                'barber_id'      => $app->barber_id,
                'appointment_id' => $id,
                'inv'            => $inv,
                'debit'          => $amount,
                'credit'         => 0,
                'com_amount'     => 0,
                'pay_status'     => "REFUND",
                'description'    => 'Refund Payment',
            );
        }


        $run = Wallet::create($paymentData);
        if($run){
           $app->refund = true;
           $up          = $app->update();

            if($up){
                $log = [
                    'appointment_id' => $app->id,
                    'status'         => $logStatus,
                    'payment'        => $amount,
                ];
                AppointmentLog::create($log);

                Mail::to($app->customer->email)->send(new RefundPayment());
                return redirect()->back();
            }
        }


    }
}
