<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BarberDoc;
use App\Models\Barber;
use App\Models\BarberTimeSlot;
use App\Models\Appointment;
use App\Models\Wallet;
use App\Models\Service;
use App\Models\ProductWallet;
use Intervention\Image\ImageManagerStatic as Image;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $userid    = Auth::user()->id;
        $user      = Barber::where('user_id',$userid)->first();
        $docs      = BarberDoc::where('barber_id',$userid)->get();
        $slots     = BarberTimeSlot::where('barber_id',$userid)->with('barber')->get();
        $totalapp  = Appointment::where('salon_id',$userid)->count();
        $completed = Appointment::where([
            'salon_id' => $userid,
            'status' => 'Completed'
        ])->count();

        // canceled Appointmnet
        $canceled = Appointment::where([
            'salon_id' => $userid,
            'status' => 'Canceled'
        ])->count();

        $services = Service::where('user_id',$userid)->orderBy('title','ASC')->get();

        $businessBarber =  Barber::where(['is_business'=> false,'barber_of'=>$userid])->get();

        // $wallet = Wallet::where(['barber_id' => $userid,'pay_status'=>'UNPAID'])->selectRaw('SUM(debit) - SUM(credit) as total')->first();

        return view('admin.barbar.profile',get_defined_vars());
    }

    public function barberprofileadmin($id)
    {
        //
        $userid      = $id;
        $user        = Barber::where('user_id',$userid)->first();
        $salonbarber = Barber::where('barber_of',$userid)->get();
        $docs        = BarberDoc::where('barber_id',$userid)->get();
        $slots       = BarberTimeSlot::where('barber_id',$userid)->with('barber')->get();


        $totalapp  = Appointment::where('barber_id',$userid)->count();
        $completed = Appointment::where([
            'barber_id' => $userid,
            'status'    => 'Completed'
        ])->count();

        // canceled Appointmnet
        $canceled = Appointment::where([
            'barber_id' => $userid,
            'status'    => 'Canceled'
        ])->count();

        $services     = Service::where('user_id',$id)->get();
        $wallet       = Wallet::where(['barber_id' => $userid,'pay_status'=>'UNPAID'])->selectRaw('SUM(debit) - SUM(credit) as total')->first();
        $wallet_total = Wallet::where('barber_id',$userid)->selectRaw('SUM(debit) as totalamount')->first();
        
        
        $comm        = Wallet::where(['barber_id' => $userid,'pay_status'=>'UNPAID'])->selectRaw('SUM(com_amount) current_com')->first();
        $comm_tootal = Wallet::where('barber_id',$userid)->selectRaw('SUM(com_amount) as totalcom')->first();

        
        $product_com = ProductWallet::where(['barber_id' => $userid,'pay_status'=>'UNPAID'])->selectRaw('SUM(com_amount) current_com')->first();
        $product_sale = ProductWallet::where('barber_id',$userid)->selectRaw('SUM(debit) - SUM(credit) as totalPro')->first();
        // return $product_sale;
        return view('admin.barbar.barber_profile_admin',get_defined_vars());
    }

    public function adddocumetns(Request $request)
    {

        try {


            $data = array(
                'barber_id' => Auth::user()->id,
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
                return redirect()->route('profile')->with('message','Documents Added');
            }

        } catch (\Exception $e) {

            return $e->getMessage();
        }
    }

    public function addslot(Request $request)
    {
        // dd($request->all());
        try {

            $data = array(
                'barber_id' => Auth::user()->id,
                'slot_no'   => $request->slotno,
                'from_time' => $request->fromtime,
                'to_time'   => $request->totime,
            );
            if($request->fromtime < $request->totime){
                $result = BarberTimeSlot::create($data);
                
            }else{
                return back()->with('time','From Time is Later Than To Time');
            }


            if($result){
                return redirect()->route('profile')->with('message','Slot Added');
            }

        } catch (\Exception $e) {

            return $e->getMessage();
        }
    }

    public function deleteslot($id)
    {
        try {
            $row = BarberTimeSlot::find($id);
            $result = $row->delete();
            if ($result) {
                return redirect()->route('profile')->with('message','Slot Deleted');
            }
        } catch (\Excetion $e) {
            return $e->getMessage();
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
        $barber = Barber::find($id);
        return view('admin.barbar.editprofile',compact('barber'));
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

        // dd($request->all());
        
        try {

            $row = Barber::find($id);

            
            
            if ($request->hasFile('image')) {

                $image    = $request->file('image');
                $filename = $image->getClientOriginalName();
                
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
                // return "img wala";

            }else{
                
                $row->contact = $request->contact;
                $row->salon = $request->salon;
                $row->barber_type = $request->type;
                $row->account_title = $request->accounttitle;
                $row->account_no = $request->accountno;
                $row->credit_card = $request->creditcard;
                $result = $row->update();
            }

            if ($result) {
                return redirect()->route('profile')->with('message','Personal Information Updates');
            }
            
            

        } catch (\Exception $e) {

            return $e->getMessage();
        }
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


    public function barberappointmenthistory($id)
    {
        $barber = Barber::where('user_id',$id)->with('appoitment')->first();
        return view('admin.barbar.barber_appointment_history',get_defined_vars());
    }


    // barber wallet History
    public function barberwallethistory($id)
    {
        $barber = Barber::where('user_id',$id)->with('wallet')->first();
        return view('admin.barbar.barber_wallet_history',get_defined_vars());
    }


    public function addBusnissBarber(Request $request)
    {

        // dd($request->all());
        try {

            if ($request->hasFile('image')) {
                $data = array(
                    'name'          => $request->name,
                    'email'         => $request->email,
                    'contact'       => $request->contact,
                    'account_title' => $request->account_title,
                    'account_no'    => $request->account,
                    'credit_card'   => $request->credit,
                    'email'         => Auth::user()->email,
                    'is_business'   => false,
                    'barber_of'     => Auth::user()->id,
                    'status'        => 'Active'
                );
    
                $image        = $request->file('image');
                $filename     = $image->getClientOriginalName();
                $image_resize = Image::make($image->getRealPath());
                $image_resize->resize(200, 200);
                $image_resize->save(public_path('barberDoc/' .$filename));
                $data['img'] = $filename;
    
                $result = Barber::create($data);
    
    
                if ($result){
                    return  redirect()->route('profile');
                }

            }
           
            
        } catch (\Excetion $e) {
            return $e->getMessage();
        }
    }
}
