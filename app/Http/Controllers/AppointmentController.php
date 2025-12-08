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
use Illuminate\Support\Facades\Log;
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

    // public $fcmController;

    public function __construct()
    {
        // $this->fcmController = new FcmController();
    }
    public function index()
    {
        //
        $appointments = Appointment::with('customer', 'service', 'slot', 'barber', 'rating', 'log')->orderBy('created_at', 'desc')->get();
        // return $appointments;
        return view('admin.appointment.index', compact('appointments'));
    }


    public function barberAppointment()
    {
        $userid = Auth::user()->id;

        // Total Appointment
        $appointments = Appointment::where('salon_id', $userid)->with('customer', 'barber', 'service', 'slot', 'reason', 'rating')->orderBy('created_at', 'desc')->get();
        // return $appointments;
        return view('admin.barbar.appointments', compact('appointments'));
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
                'address'   => $request->appType == 'Mobile_shop' ? 'required' : '',
                'slote'     => 'required',
                'lat'       => $request->appType == 'Mobile_shop' ? 'required' : '',
                'lng'       => $request->appType == 'Mobile_shop' ? 'required' : '',
                'appType'   => 'required',
                'town'    => $request->appType == 'Mobile_shop' ? 'required' : '',
                'postcode' => $request->appType == 'Mobile_shop' ? 'required' : '',
                'address2' => $request->appType == 'Mobile_shop' ? 'required' : '',
            ];

            $validateData = Validator::make($request->all(), $role);

            if ($validateData->fails()) {

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
                'salon_id'     => $barber->barber_of,
                'payment_status' => "pending"
            );

            // $slot = BarberTimeSlot::where('barber_id',$request->barber_id)->where('id', $request->slote)->where('status', 'Avalible')->first();

            // if(!$slot){
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Slot Not Available',
            //     ]);
            // }
            // return response()->json(Auth::user()->email);

            $result = Appointment::create($data);

            // $slot->status = 'Unavailable';

            // $slot->update();

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
                try {
                    Mail::to(Auth::user()->email)->send(new AppointmentMail($usermaildata));
                    Log::info('Appointment mail sent successfully to user: ' . Auth::user()->email);
                } catch (\Exception $e) {
                    Log::error('Failed to send appointment mail to user: ' . Auth::user()->email . ' Error: ' . $e->getMessage());
                }

                if (Auth::user()->device_token != null) {
                    // $this->fcmController->sendNotification(new \Illuminate\Http\Request([
                    //     'token' => Auth::user()->device_token,
                    //     'title' => 'New Appointment',
                    //     'body' => 'Your appointment has been booked.',
                    //     'email' => Auth::user()->email,
                    // ]));
                }

                $barber = Barber::find($request->barber_id);
                $user = User::find($barber->barber_of);

                if ($user->device_token != null) {

                    // $this->fcmController->sendNotification(new \Illuminate\Http\Request([
                    //     'token' => $user->device_token,
                    //     'title' => 'New Appointment',
                    //     'body' => 'You have a new appointment request.',
                    //     'email' => $user->email,
                    // ]));
                }

                if ($barber->device_token != null) {

                    // $this->fcmController->sendNotification(new \Illuminate\Http\Request([
                    //     'token' => $barber->device_token,
                    //     'title' => 'New Appointment',
                    //     'body' => 'You have a new appointment request.',
                    //     'email' => $barber->email,
                    // ]));
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


    public function appointmentajax(Request $request)
    {
        $role = [
            'time'      => 'required',
            'slote'     => 'required'

        ];

        $validateData = Validator::make($request->all(), $role);

        if ($validateData->fails()) {

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
        } else {
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
            $app         = Appointment::where('customer_id', $authid)->with('slot', 'customer', 'barber')->orderby('id', 'desc')->get();
            $products    = ProductWallet::where('customer_id', $authid)->with('code')->orderby('id', 'desc')->get();
            $data = array_merge($app->toArray(), $products->toArray());
            usort($data, function ($a, $b) {
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


    public function updateappointment(Request $request, $id)
    {
        if (!$id || $id == null || $id == '') {
            return response()->json([
                'success' => false,
                'Error' => 'Invalid appointment ID'
            ]);
        }

        try {

            $role = [
                'time'      => 'required',
                'barber_id' => 'required',
                'type'      => 'required',
                'service'   => 'required',
                'address'   => $request->appType == 'Mobile_shop' ? 'required' : '',
                'slote'     => 'required',
                'lat'       => $request->appType == 'Mobile_shop' ? 'required' : '',
                'lng'       => $request->appType == 'Mobile_shop' ? 'required' : '',
                'appType'   => 'required',
                'town'    => $request->appType == 'Mobile_shop' ? 'required' : '',
                'postcode' => $request->appType == 'Mobile_shop' ? 'required' : '',
                'address2' => $request->appType == 'Mobile_shop' ? 'required' : '',
            ];

            $validateData = Validator::make($request->all(), $role);

            if ($validateData->fails()) {

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

            $result = Appointment::where('id', $id)->update($data);

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

                if (Auth::user()->device_token != null) {
                    // $this->fcmController->sendNotification(new \Illuminate\Http\Request([
                    //     'token' => Auth::user()->device_token,
                    //     'title' => 'Appointment Updated',
                    //     'body' => 'Your appointment has been updated.',
                    //     'email' => Auth::user()->email,
                    // ]));
                }

                $barber = Barber::find($request->barber_id);
                $user = User::find($barber->barber_of);
                if ($user->device_token != null) {

                    // $this->fcmController->sendNotification(new \Illuminate\Http\Request([
                    //     'token' => $user->device_token,
                    //     'title' => 'Updated Appointment',
                    //     'body' => 'You have an updated appointment.',
                    //     'email' => $user->email,
                    // ]));
                }

                $log = [
                    'appointment_id' => (string)$id,
                    'status'         => "ADD",
                    'payment'        => 0,
                ];
                AppointmentLog::create($log);

                return response()->json([
                    'success' => true,
                    'app_id'  => (string)$id,
                ]);
            }
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'Erorr'   => $e->getMessage(),
            ]);
        }
    }


    public function countnotification()
    {
        $authid = Auth::user()->id;

        try {
            $app         = Appointment::where('customer_id', $authid)->where('view', 'unview')->get();
            $products    = ProductWallet::where('customer_id', $authid)->where('view', 'unview')->get();
            $count_app = $app->count();
            $count_product = $products->count();
            $count_notification = $count_app + $count_product;
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
        try {
            // Check if appointment exists
            if (!$appointment) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Appointment not found.'
                ], 404);
            }

            // Delete related records first to maintain referential integrity
            

            // Delete the appointment
            $appointment->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Appointment deleted successfully.'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error deleting appointment: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while deleting the appointment.'
            ], 500);
        }
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



            if ($run) {

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

        $app  = Appointment::with('wallet', 'customer')->find($id);
        $user = Auth::user();
        // Set your secret key. Remember to switch to your live secret key in production.
        // See your keys here: https://dashboard.stripe.com/apikeys
        \Stripe\Stripe::setApiKey('sk_test_51IUEm1Gp0YSIUkCJnomc7ayDjucczKUbUu6alcJNb2TteQ7jHV0PWk4CtE93AjiQmdcAZN2QxKIlS4G2odVnNIU300RiJ2suuU');

        // Token is created using Stripe Checkout or Elements!
        // Get the payment token ID submitted by the form:
        // $token  = $app->stripe_token;
        $token  = $app->stripe_token;
        $logStatus = '';
        if ($app->cancel_payment) {
            $amount    = ($app->wallet->debit + $app->wallet->com_amount) - 6;
            $logStatus = "PARTIAL-REFUND";
        } else {
            $logStatus = "TOTAL-REFUND";
            $amount    = ($app->wallet->debit + $app->wallet->com_amount);
        }


        $refund =  \Stripe\Refund::create([
            'charge'   => $token,
            'amount'   => $amount * 100,
        ]);




        Wallet::where('appointment_id', $id)->delete();
        $mixid  = Wallet::max('inv');
        $inv    = $mixid + 1;

        if ($app->cancel_payment) {
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
        } else {
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
        if ($run) {
            $app->refund = true;
            $up          = $app->update();

            if ($up) {
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
