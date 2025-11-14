<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BarberDoc;
use App\Models\Barber;
use App\Models\Rating;
use App\Models\BarberTimeSlot;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\User;
use App\Models\Wallet;
use App\Models\ProductWallet;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

use Intervention\Image\ImageManagerStatic as Image;

class BarberApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */




    public function dashboard()
    {
        //
        $userid = Auth::user()->id;
        // $docs   = BarberDoc::where('barber_id',$userid)->get();
        // $slots  = BarberTimeSlot::where('barber_id',$userid)->get();

        $totalapp = Appointment::where('salon_id',$userid)->count();

        $completed = Appointment::where([
            'salon_id' => $userid,
            'status' => 'Completed'
        ])->count();

        // canceled Appointmnet
        $canceled = Appointment::where([
            'salon_id' => $userid,
            'status' => 'Canceled'
        ])->count();

        $wallet = Wallet::where('salon_id',$userid)->selectRaw('SUM(debit) - SUM(credit) as total')->first();
            $payment = '';
            if($wallet->total == null){
                $payment = 0;
            }else{
                $payment = $wallet->total;

            }
        return  response()->json([
            'appointments' => $totalapp,
            'completedApp' => $completed,
            'cancleApp'    => $canceled,
            'wallet'       => $payment,
        ]);

    }

    // Add Document 
    public function adddocumetns(Request $request)
    {

        try {

            $role = [
                
                'title' => 'required',
                'type'  => 'required',
            ];

            $validateData = Validator::make($request->all(),$role);

            if($validateData->fails()){

                return response()->json([
                    'message' => 'Invalid data send',
                    'Error' => $validateData->errors(),
                ], 400);

            }


            $data = array(
                'barber_id' => Auth::user()->user_id,
                'title'     => $request->title,
                'type'      => $request->type,
            );

            if ($request->type == 'PDF') {

                $fileName = time().'.'.$request->image->extension();
                $request->image->move(public_path('barberDoc'), $fileName);
                $data['image'] = $fileName;
                
            }else{  

                if($request->hasFile('image')) {
                    $image       = $request->file('image');
                    $filename    = $image->getClientOriginalName();
                
                    $image_resize = Image::make($image->getRealPath());              
                    $image_resize->resize(300, 300);
                    $image_resize->save(public_path('barberDoc/' .$filename));
                    $data['image'] = $filename;
                
                }

            }
            $result = BarberDoc::create($data);

            if($result){
                return  response()->json([
                    'success' => true,
                    'Message' => "Document Added",
                ]);
            }

        } catch (\Exception $e) {

            return  response()->json([
                'success' => false,
                'Error'   => $e->getMessage(),
            ]);

        }
    }

    public function documents()
    {
        try {
           
            $userid    = Auth::user()->user_id;
            $documents = BarberDoc::where('barber_id',$userid)->get();

            return  response()->json($documents);

        } catch (\Exception $e) {

            return  response()->json([
                'success' => false,
                'Error'   => $e->getMessage(),
            ]);
        }
    }

    // All  Slot
    public function slots()
    {
        try {
            $slots = BarberTimeSlot::where('barber_id',Auth::user()->user_id)->with('barber')->get();

            return response()->json([
                'success' => true,
                'data'    => $slots,
                'status'  => 200,
            ]);

        } catch (\Exception $e) {
            return  response()->json([
                'success' => false,
                'Error'   => $e->getMessage(),
            ]);
        }
    }

    // check available slots
    public function checkavailableslots($barberId)
    {
        try {
            $slots = BarberTimeSlot::where('barber_id',$barberId)->where('status', 'Avalible')->get();

            return response()->json([
                'success' => true,
                'data'    => $slots,
                'status'  => 200,
            ]);

        } catch (\Exception $e) {
            return  response()->json([
                'success' => false,
                'Error'   => $e->getMessage(),
            ]);
        }
    }

    // Add Slot
    public function addslot(Request $request)
    {
        try {

            $role = [  
                'slot_no'   => 'required',
                'from_time' => 'required',
                'to_time'   => 'required',
            ];

            $validateData = Validator::make($request->all(),$role);

            if($validateData->fails()){

                return response()->json([
                    'message' => 'Invalid data send',
                    'Error' => $validateData->errors(),
                ], 400);

            }

            $data = array(
                'barber_id' => Auth::user()->user_id,
                'slot_no'   => $request->slot_no,
                'from_time' => $request->from_time,
                'to_time'   => $request->to_time,
            );

            
            if($request->from_time < $request->to_time){

                $result = BarberTimeSlot::create($data);

                return  response()->json([
                    'success' => true,
                    'Message' => "Slot Added",
                ]);

            }else{

                return  response()->json([
                    'success' => false,
                    'Message' => 'From Time is Later Than To Time',
                ]);
            }

        } catch (\Exception $e) {

             return response()->json([
                'success' => false,
                'Error'   => $e->getMessage(),
            ]);
        }
    }

    // Delete Slot
    public function deleteslot($id)
    {
        try {
            $row = BarberTimeSlot::find($id);
            $result = $row->delete();
            if ($result) {

                return  response()->json([
                    'success' => true,
                    'Message' => 'Slot Deleted',
                ]);
            }
        } catch (\Excetion $e) {
            return  response()->json([
                'success' => false,
                'Error'   => $e->getMessage(),
            ]);
        }
    }

    // Barber Apointment
    public function barberAppointment()
    {
        $userid = Auth::user()->id;
        $appointments = Appointment::where('salon_id',$userid)->with('customer','service','slot','barber')->orderBy('date','desc')->get();

        return  response()->json([
            'success' => true,
            'data' => $appointments,
        ]);
    }

    public function completeAppoint($id)
    {
        try {
            
            $row = Appointment::find($id);

            $row->status = 'Completed';
            $result = $row->update();

            return  response()->json([
                'success' => true,
                'Message' => "Appointment Completed",
            ]);



        } catch (\Exception $e) {

            return  response()->json([
                'success' => false,
                'Error'   => $e->getMessage(),
            ]);
        }
    }
    // Barber Payment History
    public function barberpayment()
    {
        $userid = Auth::user()->id;

        $payments = Wallet::where('salon_id',$userid)->get();

        return  response()->json([
            'success' => true,
            'data' => $payments,
        ]);
    }


    public function updateprofile(Request $request)
    {

        $role = [ 
            'id'           => 'required',
            'contact'      => 'required',
            'salon'        => 'required',
            'type'         => 'required',
            'accounttitle' => 'required',
            'accountno'    => 'required',
            'creditcard'   => 'required',
        ];

        $validateData = Validator::make($request->all(),$role);

        if($validateData->fails()){

            return response()->json([
                'message' => 'Invalid data send',
                'Error' => $validateData->errors(),
            ], 400);

        }


        try {

            $row = Barber::find($request->id);

            if ($request->hasFile('image')) {

                $image        = $request->file('image');
                $filename     = $image->getClientOriginalName();
                $image_resize = Image::make($image->getRealPath());
                $image_resize->resize(200, 200);
                $image_resize->save(public_path('barberDoc/' .$filename));
                $row->img           = $filename;

                $row->contact       = $request->contact;
                $row->salon         = $request->salon;
                $row->barber_type   = $request->type;
                $row->account_title = $request->accounttitle;
                $row->account_no    = $request->accountno;
                $row->credit_card   = $request->creditcard;
                $result             = $row->update();

            }else{
                
                $row->contact       = $request->contact;
                $row->salon         = $request->salon;
                $row->barber_type   = $request->type;
                $row->account_title = $request->accounttitle;
                $row->account_no    = $request->accountno;
                $row->credit_card   = $request->creditcard;

                $result = $row->update();

            }

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


    	   // update appointments
           public function update_app(Request $request){
            try {
            $store = Appointment::find($request->id);
            $store->barberview = $request->barberview;
            $store->update();
            return response()->json(['status' =>'appointment barber updated']);
    
        } catch (\Exception $e) {
    
            return response()->json([
                'success' => false,
                'Error' => $e->getMessage()
            ]);
    
        }
        }
    
     // update product wallet
     public function update_wallet(Request $request){
        try {
        $store = ProductWallet::find($request->id);
        $store->barberview = $request->barberview;
        $store->update();
        return response()->json(['status' =>'product wallet barber updated']);
    
    } catch (\Exception $e) {
    
        return response()->json([
            'success' => false,
            'Error' => $e->getMessage()
        ]);
    
    }
    }
        
        // for notification
    public function barber_notification()
    {
        $authid = Auth::user()->id;
    
        try {
            $app         = Appointment::where('salon_id',$authid)->with('slot')->orderby('id','desc')->get();
            return response()->json($app);
    
        } catch (\Exception $e) {
    
            return response()->json([
                'success' => false,
                'Error' => $e->getMessage()
            ]);
    
        }
    }
        
        public function barber_countnotification()
    {
        $authid = Auth::user()->id;
    
        try {
            $app         = Appointment::where('salon_id',$authid)->where('barberview','unview')->get();
            $count_notification = $app->count();
            return response()->json($count_notification);
    
        } catch (\Exception $e) {
    
            return response()->json([
                'success' => false,
                'Error' => $e->getMessage()
            ]);
    
        }
    }

    public function notificatoin()
    {

        try {  
            $date = date('Y-m-d');

            $appoi = Appointment::where([
                'status' => 'Paid',
                'date' => $date
                ])->with('slot')->get();



            $current = \Carbon\Carbon::now();
            $timeInMilliseconds = $current->getPreciseTimestamp(3);

            $app = \Carbon\Carbon::parse($appoi[0]->date.$appoi[0]->slot->from_time)->getPreciseTimestamp(3);
            $c = $app -  $timeInMilliseconds;

            // 3,600,000
            $diff= \Carbon\Carbon::createFromTimestampMs('1465890269667')->toDateTimeString();

            return response()->json([
                'current' => $timeInMilliseconds,
                'app'     => $app,
                'diff'    => $diff,
            ]);

            // $SERVER_API_KEY = 'AAAA3HaTkiY:APA91bH7w0D8dBQDGLith9YEOMwbW6y-uUPabDzaDp8uos84uIDIAryeWUU9o3d7KdczvjlC-8GrqCZcIpT1Qj_j1mjP-DmGXFSkbfthAp2ZDKBG6QtQ2B3zVLvDBKwnH6ANfnwau3fL';
            // $token = 'ez-y8W3sRJS2lSbvnvIOwe:APA91bHyBBlwXt82liI5qR-cWUaBsFOIjMMbBc5J04j43JGv9wU4D1HFdC15eroTtqIdzb7niEt9zR5kAJdTa1M8VPvkIDXiCALlTi7FhbqzINO3fnLRc37W-b49Rvr11UKFS6okLRoa';
            // $data = [
            //     "registration_ids" =>   array (
            //         $token
            //     ),
            //     "notification" => [
            //         "title" => "Appointment Alert",
            //         "body" => 'testing Schaduler',  
            //     ]
            // ];
            // $dataString = json_encode($data);
        
            // $headers = [
            //     'Authorization: key='.$SERVER_API_KEY,
            //     'Content-Type: application/json',
            // ];
        
            // $ch = curl_init();
          
            // curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            // curl_setopt($ch, CURLOPT_POST, true);
            // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
                   
            // $response = curl_exec($ch);
      
            // return response()->json([
            //     'success' => true,
            //     'message' => $response,
            // ]);


        } catch (\Exception $e) {
            
            return response()->json([
                'success' => false,
                'Error' => $e->getMessage(),
            ]);
        }
    }


    public function request_change_password_barber(Request $request)
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
            $user = User::where('email','=',$request->email)->first();

            if ($user) {

                return  response()->json([
                    'success' => true,
                    'id'      => $user->id,
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

    public function request_update_password_barber(Request $request)
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

            $user           = User::find($request->id);
            $user->password = bcrypt($request->password);
            $result         = $user->update();

           if($result){
                $barber           = Barber::where('user_id',$request->id)->first();
                $barber->password = bcrypt($request->password);
                $result2          = $barber->update();
           }

            if ($result2) {

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


    // Services
     public function services()
     {
         try {
             $services = Service::where('user_id',Auth::user()->id)->get();
 
             return response()->json($services);
 
         } catch (\Excetion $e) {
             return  response()->json([
                 'success' => false,
                 'Error'   => $e->getMessage(),
             ]);
         }
     }
 
     // Add Service
     public function addservice(Request $request)
     {
         try {
 
             $role = [  
                 'title'   => 'required',
                 'price'   => 'required',
             ];
 
             $validateData = Validator::make($request->all(),$role);
 
             if($validateData->fails()){
 
                 return response()->json([
                     'message' => 'Invalid data send',
                     'Error'   => $validateData->errors(),
                 ], 400);
 
             }
 
      $salon = Barber::where('user_id',Auth::user()->user_id)->first();
             $data = array(
                 'user_id' => $salon->id,
                 'title'   => $request->title,
                 'price'   => $request->price,
             );
             
            $result = Service::create($data);

            if($result){
                return  response()->json([
                    'success' => true,
                    'Message' => "Service Added",
                ]);
            }
            
         } catch (\Exception $e) {
 
              return response()->json([
                 'success' => false,
                 'Error'   => $e->getMessage(),
             ]);
         }
     }


     // Delete Slot
    public function servicedelete($id)
    {
        try {
            $row = Service::find($id);
            $result = $row->delete();
            if ($result) {

                return  response()->json([
                    'success' => true,
                    'Message' => 'Service Deleted',
                ]);
            }
        } catch (\Excetion $e) {
            return  response()->json([
                'success' => false,
                'Error'   => $e->getMessage(),
            ]);
        }
    }


    public function addBusnissBarber11(Request $request)
    {

        $role = [
            
            'email'         => 'email|required',
            'name'          => 'required',
            'contact'       => 'required',
            'account_title' => 'required',
            'account_no'    => 'required',
            'credit'        => 'required',
            'image'         => 'required',

        ];

        $validateData = Validator::make($request->all(),$role);

        if($validateData->fails()){

            return response()->json([
                'message' => 'Invalid data send',
                'Error'   => $validateData->errors(),
            ],400);
        }

        try {

            if ($request->hasFile('image')) {
                $data = array(
                    'name'          => $request->name,
                    'email'         => $request->email,
                    'contact'       => $request->contact,
                    'account_title' => $request->account_title,
                    'account_no'    => $request->account,
                    'credit_card'   => $request->credit,
                    'is_business'   => false,
                    'barber_of'     => Auth::user()->id,
                    'status'        => 'Active',
                );
    
                 $image        = $request->file('image');
                $filename     = $image->getClientOriginalName();
                $image_resize = Image::make($image->getRealPath());
                $image_resize->resize(200, 200);
                $image_resize->save(public_path('barberDoc/' .$filename));
                $data['img'] = $filename;

                $result = Barber::create($data);
    
    
                if ($result){
                    return  response()->json([
                        'success' => true,
                        'Message' => 'Barber Added To Salon',
                    ]);
                }

            }else{
                return  response()->json([
                    'success' => false,
                    'Message' => 'Image is not a File',
                ]);
            }
           

           
        } catch (\Excetion $e) {
            return  response()->json([
                'success' => false,
                'Error'   => $e->getMessage(),
            ]);
        }
    }
    
    
public function addBusnissBarber(Request $request)
    {   
        
        // dd($request);

        $role = [
            
            'email'            => 'email|required',
            'first_name'       => 'required',
            'surname'         => 'required',
            'years_of_experience' =>'required',
            'contact'          => 'required',
            'employement_type' =>'required',
            // 'account_title'    => 'required',
            // 'account_no'       => 'required',
            // 'credit'           => 'required',
            'image'            => 'required',

        ];

        $validateData = Validator::make($request->all(),$role);

        if($validateData->fails()){

            return response()->json([
                'message' => 'Invalid data send',
                'Error'   => $validateData->errors(),
            ],400);
        }

        try {

            if ($request->hasFile('image')) {
                $data = array(
                    'first_name'          => $request->first_name,
                    'surname'          =>$request->surname,
                     'name'             =>($request->first_name ?: '') . ' ' . ($request->surname ?: ''),
                    'email'         => $request->email,
                    'contact'       => $request->contact,
                    'years_of_experience'=>$request->years_of_experience,
                    'employement_type' =>$request->employement_type,
                    'account_title' => $request->account_title ?? '',
                    'account_no'    => $request->account ?? '',
                    'credit_card'   => $request->credit ?? '',
                    'is_business'   => false,
                    'barber_of'     => Auth::user()->id,
                    'status'        => 'Active',
                );
    
                 $image        = $request->file('image');
                $filename      = $image->getClientOriginalName();
                $image_resize  = Image::make($image->getRealPath());
                $image_resize->resize(200, 200);
                $image_resize->save(public_path('barberDoc/' .$filename));
                $data['img'] = $filename;

                $result = Barber::create($data);
    
    
                if ($result){
                    return  response()->json([
                        'success' => true,
                        'Message' => 'Barber Added To Salon',
                    ]);
                }

            }else{
                return  response()->json([
                    'success' => false,
                    'Message' => 'Image is not a File',
                ]);
            }
           

           
        } catch (\Excetion $e) {
            return  response()->json([
                'success' => false,
                'Error'   => $e->getMessage(),
            ]);
        }
    }

    // Services
    public function ShowBarberToSalon()
    {
        try {
            $services = Barber::where('barber_of',Auth::user()->id)->get();

            return response()->json($services);

        } catch (\Excetion $e) {
            return  response()->json([
                'success' => false,
                'Error'   => $e->getMessage(),
            ]);
        }
    }
    
     public function deletebarber($id)
    {
        try {
            $result = Barber::find($id);
            $delete=public_path($result->image);
  
            if(File::exists($delete)){
                File::delete($delete);
               }
               $result->delete();
               if ($result) {
                $slots = BarberTimeSlot::where('slot_no',$id)->delete();

               }
            return response()->json(['status' => 'Barber Deleted Successfully']);

        } catch (\Excetion $e) {
            return  response()->json([
                'success' => false,
                'Error'   => $e->getMessage(),
            ]);
        }
    }

    // Services
    public function barberRating($id)
    {
        try {
            $rating = Rating::where('barber_id',$id)->with('customer')->with('salon_info.customer')->get();

            return response()->json($rating);

        } catch (\Excetion $e) {
            return  response()->json([
                'success' => false,
                'Error'   => $e->getMessage(),
            ]);
        }
    }
   
}
