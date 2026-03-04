<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\Appointment;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class RatingController extends Controller
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
        try {
            $user        = Auth::user();
            $app         = Appointment::find($request->app_id);
            $app->status = 'Review';
            $app->update(); 

            $row            = new Rating();
            $row->user_id   = $user->id;
            $row->barber_id = $app->barber_id;
            $row->app_id    = $request->app_id;
            $row->salon_id  = $app->salon_id;
            $row->rating    = $request->value;
            $row->review    = $request->review;
            $result         = $row->save();
            
            
            if($result){
                return response()->json([
                    'success' => true,
                    'Message' => "Rating Added",
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
     * Display the specified resource.
     *
     * @param  \App\Models\Rating  $rating
     * @return \Illuminate\Http\Response
     */
    public function show(Rating $rating)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Rating  $rating
     * @return \Illuminate\Http\Response
     */
    public function edit(Rating $rating)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Rating  $rating
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Rating $rating)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Rating  $rating
     * @return \Illuminate\Http\Response
     */
    public function destroy(Rating $rating)
    {
        //
    }

    // get bareber ratin 
    public function getbarberrating($id)
    {
        try {
            $rating = Rating::where('barber_id',$id)->get();
            return response()->json($rating);

        } catch (\Exception $e) {
            return  response()->json([
                'success' => false,
                'Error'   => $e->getMessage(),
            ]);
        }
       
    }

  // get salon rating
  public function salon_rating($id)
  {
      try {
          $rating = Rating::where('salon_id',$id)->get();
          return response()->json($rating);

      } catch (\Exception $e) {
          return  response()->json([
              'success' => false,
              'Error'   => $e->getMessage(),
          ]);
      }
     
  }
}
