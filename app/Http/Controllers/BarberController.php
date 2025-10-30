<?php

namespace App\Http\Controllers;

use App\Models\Barber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
// use Spatie\Geocoder\Facades\Geocoder;
// use Spatie\Geocoder\Geocoder;
Use Geocoder;
use Image;
use App\Models\BarberDoc;
use App\Models\Appointment;
use App\Models\Rating;
use App\Models\BarberTimeSlot;
use App\Models\User;
use App\Models\Service;
use App\Models\Wallet;
use App\Models\Commission;
use Illuminate\Support\Facades\Mail;
use App\Mail\BarberSignUp;
use Carbon\Carbon;
use App\Models\ProductWallet;

class BarberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // For Admin Side

        try {
            
            $barbers = Barber::where('is_business',1)->with(['rating' => function ($q){
                $q->selectRaw('count(id) as reviews, barber_id')->groupBy('barber_id');
                $q->selectRaw('avg(rating) as rate, barber_id')->groupBy('barber_id');
            }])
            ->with(['wallet' => function ($q){
                $q->whereMonth('created_at', Carbon::now()->month)->selectRaw('SUM(debit) as total, barber_id')->groupBy('barber_id');
            }])
            ->with(['appoitment' => function ($q){
                $q->whereMonth('created_at', Carbon::now()->month)->selectRaw('COUNT(id) as appointments, barber_id')->groupBy('barber_id');
            }])
            ->orderBy('created_at','desc')
            ->get();
            // 
            // return $barbers;
            return view('admin.barbar.index',compact('barbers'));

        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function paybarberAmount($id)
    {

        $amount = Wallet::where(['barber_id' => $id,'pay_status'=>'UNPAID'])->selectRaw('SUM(debit) - SUM(credit) as total')->first();
        $mixid  = Wallet::max('inv');
        $inv    = $mixid + 1;

        $paymentData = array(
            'user_id'        => 0,
            'barber_id'      => $id,
            'appointment_id' => 0,
            'inv'            => $inv,
            'debit'          => 0,
            'credit'         => $amount->total,
            'com_amount'     => 0,
            'description'    => 'Amount Paid to Barber',
        );

        $payResult = Wallet::create($paymentData);

        $run = Wallet::where('barber_id','=',$id)->update([
            'pay_status' => 'PAID',
        ]);

       return redirect()->back();
    }

    public function barbercommitionpayment($id)
    {

        $amount = ProductWallet::where(['barber_id' => $id,'pay_status'=>'UNPAID'])->selectRaw('SUM(com_amount) as total')->first();
        $mixid  = ProductWallet::max('inv');
        $inv    = $mixid + 1;

        $paymentData = array(
            'barber_id'   => $id,
            'customer_id' => 0,
            'order_id'    => 0,
            'inv'         => $inv,
            'debit'       => 0,
            'credit'      => $amount->total,
            'com_amount'  => 0,
            'description' => 'Paid Commission to Barber',
        );

        $payResult = ProductWallet::create($paymentData);
        
        $run = ProductWallet::where('barber_id','=',$id)->update([
            'pay_status' => 'PAID',
        ]);

       return redirect()->back();
    }

    public function signup(Request $request){
        
      

        try {
            
            $role = [
                
                'name'     => 'required|min:3',
                'email'    => 'email|required|unique:users,email',
                'contact'  => 'required',
                'address'  => 'required',
                'salon'    => 'required',
                'lat'      => 'required',
                'lng'      => 'required',
                // 'address2'  =>'requried',
                // 'town'     =>  'required',
                // 'postcode' =>'required',
                'password' => 'required|confirmed|min:8',
                // 'password' => 'required',
            ];

            $validateData = Validator::make($request->all(),$role);

            if($validateData->fails()){

                return response()->json([
                    'message' => 'Invalid data send',
                    'Error'   => $validateData->errors(),
                ], 400);

            }

            if($request->token){
                $token = $request->token;
            }else{
                $token = '';
            }
            

            $userData = array(
                'name'         => $request->name,
                'email'        => $request->email,
                'password'     => bcrypt($request->password),
                'type'         => 'Barber',
                'device_token' => $token,
            );

            $user = User::create($userData);

            $data = array(
                'name'         => $request->name,
                'email'        => $request->email,
                'contact'      => $request->contact,
                'address'      => $request->address,
                'lat'          => $request->lat,
                'lng'          => $request->lng,
                'salon'        => $request->salon,
                // 'address2'     => $request->address2,
                // 'town'         => $request->town,
                // 'postcode'     => $request->postcode,
                'user_id'      => $user->id,
                'password'     => bcrypt($request->password),
                'device_token' => $token,

            );
            $result = Barber::create($data);

            $dataMail = array(
                'name'     => $request->name,
                'shop'     => $request->salon,
                'contact'  => $request->contact,
                'address'  => $request->address,
                'password' => $request->password,
                'email'    => $request->email,
            );

            if ($result){
                
                Mail::to($request->email)->send(new BarberSignUp($dataMail));
                return response()->json([
                    'success' => true,
                    'Message' => "Barber Register",
                ]);
            }
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'Erorr'   => $e->getMessage(),
            ]);

        }
        


        
    }

    public function barberdocumetn(Request $request)
    {
        try {

            $role = [
                
                'title' => 'required|min:3',
                'image' => 'required|image|mimes:jpeg,png,jpg',
            ];

            $validateData = Validator::make($request->all(),$role);

            if($validateData->fails()){

                return response()->json([
                    'message' => 'Invalid data send',
                    'Error'   => $validateData->errors(),
                ], 400);

            }

            $user = Auth::user();

            $data = array(
                'title'     => $request->title,
                'barber_id' => $user->id,
            );

            $destination = "/barberDoc";

            $image = $request->file('image');
            $image_uploaded_path = $image->store($destination, 'public');

            $data['image'] = basename($image_uploaded_path);

            $result = BarberDoc::create($data);

            if ($result){
                return response()->json([
                    'success' => true,
                    'Message' => "Barber Documents Added",
                ]);
            }



        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'Erorr'   => $e->getMessage(),
            ]);

        }
    }

    public function businessBarberSlot($id)
    {
        
        
        try {
            
            $slots     = BarberTimeSlot::where('slot_no',$id)->with('barber')->get();
            return response()->json($slots);


        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'Erorr'   => $e->getMessage(),
            ]);

        }
    }

    public function barberagainstbusiness($id)
    {
        try {
            
            $barbars     = Barber::where(['barber_of'=> $id,'is_business' => false])->with('slot','service')->get();
         //'salonrating',
            return response()->json(['barbers' =>$barbars]);


        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'Erorr'   => $e->getMessage(),
            ]);

        }
    }




    // Barberslote
    public function barberslote(Request $request)
    {   
        try {
            
            $role = [
                
                'from_time' => 'required',
                'to_time' => 'required',
            ];

            $validateData = Validator::make($request->all(),$role);

            if($validateData->fails()){

                return response()->json([
                    'message' => 'Invalid data send',
                    'Error'   => $validateData->errors(),
                ], 400);

            }

            $user = Auth::user();

            $data = array(
                'from_time' => $request->from_time,
                'to_time'   => $request->to_time,
                'barber_id' => $user->id,
            );
            $result = BarberTimeSlot::create($data);

            if ($result){
                return response()->json([
                    'success' => true,
                    'Message' => "Barber Time Slot Added",
                ]);
            }


        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'Erorr'   => $e->getMessage(),
            ]);

        }
        
    }



    public function Barbers(Request $request)
    {
        try {
                           
            $haversine = "(6371 * acos(cos(radians($request->lat))
                        * cos(radians(barbers.lat))
                        * cos(radians(barbers.lng)
                        - radians($request->lng))
                        + sin(radians($request->lat))
                        * sin(radians(barbers.lat))))";
                            
             $barbers = Barber::where(['status' => 'Active','is_business' => true])->select('*')
               ->selectRaw("{$haversine} AS distance")
               ->whereRaw("{$haversine} < ?", [20])
               ->with('rating')->with('salonrating.user_info')
                  // ->with(['appoitment'=> function($q){
                //     $q->selectRaw('count(id) as booking, barber_id')->groupBy('barber_id');
                // }])
                ->orderBy('address','asc')->get();

            return response()->json($barbers);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'Erorr'   => $e->getMessage(),
            ]);

        }
        

    }


    public function barbersforhome(Request $request)
    {
        try {
                           
            $haversine = "(6371 * acos(cos(radians($request->lat))
                        * cos(radians(barbers.lat))
                        * cos(radians(barbers.lng)
                        - radians($request->lng))
                        + sin(radians($request->lat))
                        * sin(radians(barbers.lat))))";
                            
             $barbers = Barber::where('status','=','Active')->select('*')
               ->selectRaw("{$haversine} AS distance")
               ->whereRaw("{$haversine} < ?", [20])
               ->with(['rating'=> function($q){
                    $q->selectRaw('avg(rating) as totalrating, barber_id')->groupBy('barber_id');
                }])
                ->with(['appoitment'=> function($q){
                    $q->selectRaw('count(id) as booking, barber_id')->groupBy('barber_id');
                }])
                ->orderBy('address','asc')->get();

            return response()->json($barbers);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'Erorr'   => $e->getMessage(),
            ]);

        }
        

    }

    public function barberdetails($id)
    {
        try {
            
            $barber    = Barber::find($id);
            $slotes    = BarberTimeSlot::where('slot_no',$barber->id)->get();
            $rating    = Rating::where('barber_id',$barber->id)->avg('rating');
            $allrating = Rating::where('barber_id',$barber->id)->get();
            $reviews   = Rating::where('barber_id',$barber->id)->count();
            $appo      = Appointment::where('barber_id',$barber->id)->count();
            $com       = Commission::orderBy('id','desc')->first();
            $salon     = Barber::find($barber->barber_of);
            if($appo == null){
                $booking = 0;
            }else{
                $booking = $appo;
            }
            if($rating == null){
                $avgrate = "0";    
            }else{

                $avgrate= number_format($rating,1);
            }

            // $services = Service::where('user_id',$barber->barber_of)->get();
             $services = Service::where('user_id',$barber->barber_of)->get();

            return response()->json([
                'barber'   => $barber,
                'slots'    => $slotes,
                'services' => $services,
                'rating'   => $avgrate,
                'booking'  => $booking,
                'reviews'  => $reviews,
                  'com'      => $com !== null ? $com->percent : 0,
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
     * @param  \App\Models\barber  $barber
     * @return \Illuminate\Http\Response
     */
    public function show(barber $barber)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\barber  $barber
     * @return \Illuminate\Http\Response
     */
    public function edit(barber $barber)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\barber  $barber
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, barber $barber)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\barber  $barber
     * @return \Illuminate\Http\Response
     */
    public function destroy(barber $barber)
    {
        //
    }



    public function barberactivestatus($id)
    {
        $row = Barber::find($id);

        $row->status = 'Active';
        $result = $row->update();
        if ($result) {
            return redirect()->route('barbers.index');
        }
    }
}
