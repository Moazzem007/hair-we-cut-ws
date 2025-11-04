<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Barber;
use App\Models\Rating;
use Illuminate\Support\Facades\Mail;
use App\Mail\BarberSignUp;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class BarberAuthApiController extends Controller
{

    // public function registration(Request $request){

    //     $validateData = $request->validate([
    //         'name' => 'required',
    //         'email' => 'email|required|unique:users,email',
    //         'password' => 'required',
    //     ]);

    //     $validateData['password'] = bcrypt($request->password);

    //     $user  = User::create($validateData);
            
    //     $accessToken = $user->createToken('authtoken')->accessToken;


    //     return response()->json([
    //         'Data' => $user,
    //         'accessToken' => $accessToken
    //     ]);

    // }


    public function tokenupdate(Request $request)
    {
        try {

            $role = [
                    
                'token'     => 'required',
            ];

            $validateData = Validator::make($request->all(),$role);

            if($validateData->fails()){

                return response()->json([
                    'message' => 'Invalid data send',
                    'Error' => $validateData->errors(),
                ], 400);

            }

            $barber = Barber::where('id', Auth::user()->id)->first();
            


            if($barber->device_token != $request->token){

                $barber->device_token = $request->token;
                $barber->update();

                $user = User::find($barber->user_id);
                $user->device_token = $request->token;
                $user->update();

                $result = 'New Token';

            }else{
                $result = 'Old Token';
            }

            return response()->json([
                'success' => true,
                'message' => $result,
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'Erorr'   => $e->getMessage(),
            ]);
        }
        
    }

    public function registration(Request $request)
    {
        try {
            
            $role = [
                
                'name'     => 'required|min:3',
                'email'    => 'email|required|unique:users,email',
                'contact'  => 'required',
                'address'  => 'required',
                'salon'    => 'required',
                'lat'      => 'required',
                'lng'      => 'required',
                // 'town'    =>'required',
                // 'postcode'=>'required',
                // 'address2'=>'required',
                'password' => 'required|confirmed|min:8',
            ];

            $validateData = Validator::make($request->all(),$role);

            if($validateData->fails()){

                return response()->json([
                    'message' => 'Invalid data send',
                    'Error' => $validateData->errors(),
                ], 400);

            }

            $userData = array(
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => bcrypt($request->password),
                'type'     => 'Barber',
            );

            $user = User::create($userData);

            $data = array(
                'name'     => $request->name,
                'email'    => $request->email,
                'contact'  => $request->contact,
                'address'  => $request->address,
                // 'address2' => $request->address2,
                // 'town'     => $request->town,
                // 'postcode' => $request->postcode,
                'lat'      => $request->lat,
                'lng'      => $request->lng,
                'salon'    => $request->salon,
                'user_id'  => $user->id,
                'password' => bcrypt($request->password),
            );
            $result = Barber::create($data);

            $dataMail = array(
                'name'     => $request->name,
                'shop'     => $request->salon,
                'contact'  => $request->contact,
                'address'  => $request->address,
                'password' => $request->password,
                'email'    => $request->email,
                'otp'      => $request->otp ? $request->otp : 0,
            );

            if ($result){
                Mail::to($request->email)->send(new BarberSignUp($dataMail));

                 return response()->json([
                    'Success' => true,
                    'Message' => "Barber Has Register"
                ]);
            }
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'Erorr'   => $e->getMessage(),
            ]);

        }
    }




    public function login(Request $request){

        $loginData = $request->validate([
            'email'    => 'email|required',
            'password' => 'required',
        ]);

        if (! $token = auth('barber')->attempt($loginData)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json([
            'token' => $token
        ]);
    }


    public function profile(){

        $user = Auth::user();

        return response()->json($user);
    }
    
    
    // public function profile()
    // {
    //     try {
    //         $user = Auth::user();
    
    //         // Fetch the barber related to this user
    //         $barber = Barber::where('user_id', $user->id)->first();
    
    //         if (!$barber) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Barber profile not found.'
    //             ], 404);
    //         }
    
    //         return response()->json($barber);
    
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }



    // Get Barber Rating LIst
    public function getbarberratinglist($id)
    {
        try {
            $ratinglist = Rating::where('barber_id',$id)->get();
            return response()->json($ratinglist);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'Erorr'   => $e->getMessage(),
            ]);

        }
       
    }





    
}
