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
     * Store a newly created rating.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'app_id' => 'required|integer|exists:appointments,id',
                'value'  => 'required|integer|min:1|max:5',
                'review' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $user = Auth::user();
            $app  = Appointment::find($request->app_id);

            if (!$app) {
                return response()->json([
                    'success' => false,
                    'message' => 'Appointment not found',
                ], 404);
            }

            // Check if this customer already rated this appointment
            $exists = Rating::where('user_id', $user->id)
                ->where('app_id', $request->app_id)
                ->first();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already rated this appointment',
                ], 409);
            }

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

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Rating added successfully',
                    'data'    => $row,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to save rating',
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update an existing rating (only by the owner).
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'value'  => 'required|integer|min:1|max:5',
                'review' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $user   = Auth::user();
            $rating = Rating::find($id);

            if (!$rating) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rating not found',
                ], 404);
            }

            // if ($rating->user_id !== $user->id) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'You can only update your own rating',
            //     ], 403);
            // }

            $rating->rating = $request->value;
            $rating->review = $request->review;
            $rating->save();

            return response()->json([
                'success' => true,
                'message' => 'Rating updated successfully',
                'data'    => $rating,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a rating (only by the owner).
     */
    public function destroy($id)
    {
        try {
            $user   = Auth::user();
            $rating = Rating::find($id);

            if (!$rating) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rating not found',
                ], 404);
            }

            if ($rating->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only delete your own rating',
                ], 403);
            }

            $rating->delete();

            return response()->json([
                'success' => true,
                'message' => 'Rating deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all ratings for a barber.
     */
    public function getbarberrating($id)
    {
        try {
            $rating = Rating::where('barber_id', $id)->get();
            return response()->json($rating);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get all ratings for a salon.
     */
    public function salon_rating($id)
    {
        try {
            $rating = Rating::where('salon_id', $id)->get();
            return response()->json($rating);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
            ]);
        }
    }
}
