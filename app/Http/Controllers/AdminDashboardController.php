<?php

namespace App\Http\Controllers;

use App\Models\Barber;
use App\Models\Appointment;
use App\Models\Contactus;
use App\Models\Customer;
use App\Models\Wallet;
use App\Models\Rating;
use App\Models\partnercreatejob;
use App\Models\marketrentmodel;
use App\Models\marketproductsalemodel;
use App\Models\marketsalonmodel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        if (Auth::user()->type == 'Admin') {

            //  Admin dashboard
            $barbers_count     = Barber::where('status','=','Active')->count();
            $pending_barbers   = Barber::where('status','=','Pending')->orWhere('status','=','Pendding')->count();
            $customers_count   = Customer::count();
            $appoitments_count = Appointment::count();
            $contactus         = Contactus::orderBy('id','desc')->limit(3)->get();
            
            // Revenue Analysis
            $total_revenue     = Wallet::where('pay_status','!=','REFUND')->sum('debit');
            $total_commission  = Wallet::where('pay_status','!=','REFUND')->sum('com_amount');
            
            // Monthly Revenue for Chart
            $monthly_revenue = Wallet::where('pay_status','!=','REFUND')
                ->whereYear('created_at', Carbon::now()->year)
                ->selectRaw('month(created_at) as month, sum(debit) as total')
                ->groupBy('month')
                ->get()
                ->pluck('total', 'month')
                ->toArray();
                
            // Appointment Status Breakdown for Chart
            $appointment_stats = Appointment::selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->get()
                ->pluck('count', 'status')
                ->toArray();
                
            // Stats for growth (last 30 days vs previous 30 days)
            $new_users_30d = Customer::where('created_at', '>=', Carbon::now()->subDays(30))->count();
            $new_barbers_30d = Barber::where('created_at', '>=', Carbon::now()->subDays(30))->count();
                
            return view('admin.dashboard', get_defined_vars());

        }else{

            // Barber Dashboard
              $userid = Auth::user()->id;

            // Total Appointment
            $totalapp = Appointment::where([
                'salon_id' => $userid,
            ])->with('customer','service','slot')->orderBy('date','desc')->get();
            
            $totalappno = Appointment::where('salon_id',$userid)->count();
            // Completed Appointmnet
            $completed = Appointment::whereIn('status',['Completed','Review'])->where('salon_id', $userid)->count();

            // canceled Appointmnet
            $canceled = Appointment::where([
                'salon_id' => $userid,
                'status' => 'Canceled'
            ])->count();

             // Pendding Appointmnet
             $Pendding = Appointment::where([
                'salon_id' => $userid,
                'status' => 'Paid'
            ])->count();

             // Pendding Appointmnet
             $Aproved = Appointment::where([
                'salon_id' => $userid,
                'status' => 'Aproved'
            ])->count();

            $app_amounts = Appointment::where([
                'salon_id' => $userid,
                'status' => 'Completed'
            ])->with(['wallet' => function($q){
                $q->selectRaw('SUM(debit) - SUM(credit) as Total, appointment_id')->groupBy('appointment_id');
            }])
            ->with('customer')
            ->get();

            $rating = Rating::where('salon_id',$userid)->avg('rating');


            // return $rating;

            $wallet = Wallet::where('salon_id',$userid)->selectRaw('SUM(debit) - SUM(credit) as wallet')->first();

            // return $totalapp;
            return view('admin.barbar.dashboard',get_defined_vars());
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

    public function adminjobslist()
    {
        $show = partnercreatejob::orderBy('id','DESC')->get();
        return view('admin.jobs',compact('show'));
    }
        public function deletejob($id)
    {
        $show = partnercreatejob::find($id)->delete();
        return redirect()->back()->with('status','Job Deleted Successfully');
    }
    // view jobs
    public function job_view($id)
    {
        $show = partnercreatejob::find($id);
        return view('admin.jobsview',compact('show'));
    }
    public function adminmarketrentlist()
    {
        $show = marketrentmodel::orderBy('id','DESC')->get();
        return view('admin.marketplacerent',compact('show'));
    }
    public function adminmarketsalonlist()
    {
        $show = marketsalonmodel::orderBy('id','DESC')->get();
        return view('admin.marketplacesalon',compact('show'));
    }
    public function adminmarketproductlist()
    {
        $show = marketproductsalemodel::orderBy('id','DESC')->get();
        return view('admin.marketplaceproducts',compact('show'));
    }




    // view market place
    public function marketplace_productview($id)
    {
        $show = marketproductsalemodel::find($id);
        return view('admin.marketplaceproductview',compact('show'));
    }

    public function marketplace_rentview($id)
    {
        $show = marketrentmodel::find($id);
        return view('admin.marketplacerentview',compact('show'));
    }

    public function marketplace_salonview($id)
    {
        $show = marketsalonmodel::find($id);
        return view('admin.marketplacesalonview',compact('show'));
    }






}
