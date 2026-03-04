<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class CustomerAuthController extends Controller
{
    /**
     * Create a new CustomerAuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:customer', ['except' => ['login']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    
    public function login()
    {
        $credentials = request(['email', 'password']);
        if (! $token = auth('customer')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth('customer')->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'token' => $token,
            // 'token_type' => 'bearer',
            // 'expires_in' => auth('customer')->factory()->getTTL() * 60
        ]);
    }

    /**
     * Social Login for Customer
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function socialLogin(\Illuminate\Http\Request $request)
    {
        $role = [
            'provider'    => 'required|string',
            'provider_id' => 'required|string',
            'email'       => 'required|email',
            'name'        => 'required|string',
        ];

        $validateData = \Illuminate\Support\Facades\Validator::make($request->all(), $role);

        if($validateData->fails()){
            return response()->json([
                'message' => 'Invalid data send',
                'Error'   => $validateData->errors(),
            ], 400);
        }

        try {
            // Check if customer exists by provider_id
            $customer = \App\Models\Customer::where('provider_id', $request->provider_id)
                                            ->where('provider', $request->provider)
                                            ->first();

            // If not found by provider_id, check by email
            if (!$customer) {
                $customer = \App\Models\Customer::where('email', $request->email)->first();
            }

            // If still not found, create new customer
            if (!$customer) {
                $customer = \App\Models\Customer::create([
                    'name'         => $request->name,
                    'email'        => $request->email,
                    'password'     => bcrypt(\Illuminate\Support\Str::random(16)), // Dummy password
                    'contact'      => $request->contact ?? '', // Contact is required by DB schema, but might be empty from social
                    'provider'     => $request->provider,
                    'provider_id'  => $request->provider_id,
                    'device_token' => $request->token ?? '',
                ]);
            } else {
                // Update provider info if logging in via social with existing email
                if (empty($customer->provider_id)) {
                    $customer->update([
                        'provider'    => $request->provider,
                        'provider_id' => $request->provider_id,
                    ]);
                }
                
                // Update device token if provided
                if ($request->token && $customer->device_token !== $request->token) {
                    $customer->update(['device_token' => $request->token]);
                }
            }

            $token = auth('customer')->login($customer);

            return $this->respondWithToken($token);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'Error'   => $e->getMessage(),
            ], 500);
        }
    }
}