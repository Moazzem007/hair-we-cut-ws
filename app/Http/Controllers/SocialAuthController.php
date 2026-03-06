<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class SocialAuthController extends Controller
{
    /**
     * Handle Google login/registration for customers.
     *
     * The Flutter app authenticates with Google on the client side using
     * google_sign_in package and sends the user's Google profile data
     * to this endpoint.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function googleLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'google_id' => 'required|string',
            'email'     => 'required|email',
            'name'      => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid data sent',
                'errors'  => $validator->errors(),
            ], 400);
        }

        try {
            // First try to find customer by google_id
            $customer = Customer::where('google_id', $request->google_id)->first();

            if (!$customer) {
                // Check if a customer with this email already exists (registered via email/password)
                $customer = Customer::where('email', $request->email)->first();

                if ($customer) {
                    // Link Google account to existing customer
                    $customer->google_id = $request->google_id;
                    if (!$customer->login_provider) {
                        $customer->login_provider = 'google';
                    }
                    $customer->update();
                } else {
                    // Create a new customer with Google data
                    $customer = Customer::create([
                        'name'           => $request->name,
                        'email'          => $request->email,
                        'google_id'      => $request->google_id,
                        'login_provider' => 'google',
                        'password'       => null,
                        'contact'        => $request->contact ?? null,
                        'img'            => $request->photo_url ?? null,
                        'device_token'   => $request->device_token ?? null,
                    ]);
                }
            }

            // Update device token if provided
            if ($request->device_token) {
                $customer->device_token = $request->device_token;
                $customer->update();
            }

            // Generate JWT token for the customer
            $token = JWTAuth::fromUser($customer);

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'token'   => $token,
                'user'    => $customer,
                'is_new'  => $customer->wasRecentlyCreated,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Social login failed',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle Facebook login/registration for customers.
     *
     * The Flutter app authenticates with Facebook on the client side using
     * flutter_facebook_auth package and sends the user's Facebook profile data
     * to this endpoint.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function facebookLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'facebook_id' => 'required|string',
            'email'       => 'required|email',
            'name'        => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid data sent',
                'errors'  => $validator->errors(),
            ], 400);
        }

        try {
            // First try to find customer by facebook_id
            $customer = Customer::where('facebook_id', $request->facebook_id)->first();

            if (!$customer) {
                // Check if a customer with this email already exists (registered via email/password)
                $customer = Customer::where('email', $request->email)->first();

                if ($customer) {
                    // Link Facebook account to existing customer
                    $customer->facebook_id = $request->facebook_id;
                    if (!$customer->login_provider) {
                        $customer->login_provider = 'facebook';
                    }
                    $customer->update();
                } else {
                    // Create a new customer with Facebook data
                    $customer = Customer::create([
                        'name'           => $request->name,
                        'email'          => $request->email,
                        'facebook_id'    => $request->facebook_id,
                        'login_provider' => 'facebook',
                        'password'       => null,
                        'contact'        => $request->contact ?? null,
                        'img'            => $request->photo_url ?? null,
                        'device_token'   => $request->device_token ?? null,
                    ]);
                }
            }

            // Update device token if provided
            if ($request->device_token) {
                $customer->device_token = $request->device_token;
                $customer->update();
            }

            // Generate JWT token for the customer
            $token = JWTAuth::fromUser($customer);

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'token'   => $token,
                'user'    => $customer,
                'is_new'  => $customer->wasRecentlyCreated,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Social login failed',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle Apple Sign-In for customers.
     *
     * The Flutter app authenticates with Apple on the client side using
     * sign_in_with_apple package and sends the user's Apple profile data
     * to this endpoint.
     *
     * Note: Apple only provides the user's name on the FIRST sign-in.
     * On subsequent logins, only apple_id and email are available.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function appleLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'apple_id' => 'required|string',
            'email'    => 'required|email',
            'name'     => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid data sent',
                'errors'  => $validator->errors(),
            ], 400);
        }

        try {
            // First try to find customer by apple_id
            $customer = Customer::where('apple_id', $request->apple_id)->first();

            if (!$customer) {
                // Check if a customer with this email already exists
                $customer = Customer::where('email', $request->email)->first();

                if ($customer) {
                    // Link Apple account to existing customer
                    $customer->apple_id = $request->apple_id;
                    if (!$customer->login_provider) {
                        $customer->login_provider = 'apple';
                    }
                    $customer->update();
                } else {
                    // Create a new customer with Apple data
                    // Apple may not provide name on subsequent logins, so use email prefix as fallback
                    $customer = Customer::create([
                        'name'           => $request->name ?? explode('@', $request->email)[0],
                        'email'          => $request->email,
                        'apple_id'       => $request->apple_id,
                        'login_provider' => 'apple',
                        'password'       => null,
                        'contact'        => $request->contact ?? null,
                        'img'            => null,
                        'device_token'   => $request->device_token ?? null,
                    ]);
                }
            }

            // Update name if provided (Apple only sends it on first auth)
            if ($request->name && $customer->name !== $request->name) {
                $customer->name = $request->name;
                $customer->update();
            }

            // Update device token if provided
            if ($request->device_token) {
                $customer->device_token = $request->device_token;
                $customer->update();
            }

            // Generate JWT token for the customer
            $token = JWTAuth::fromUser($customer);

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'token'   => $token,
                'user'    => $customer,
                'is_new'  => $customer->wasRecentlyCreated,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Social login failed',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
