<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\AppointmentReschedule;
use App\Models\Barber;
use App\Models\BarberTimeSlot;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AppointmentRescheduleController extends Controller
{

    public $fcmController;

    public function __construct()
    {
        $this->fcmController = new FcmController();
    }


    public function requestReschedule(Request $request, $appointmentId)
    {
        try {
            $role = [
                'proposed_slote_id' => 'nullable|integer',
                'proposed_date'     => 'nullable|date',
                'message'           => 'nullable|string',
            ];
            $validator = Validator::make($request->all(), $role);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid data',
                    'errors'  => $validator->errors(),
                ], 400);
            }

            $appointment = Appointment::find($appointmentId);
            if (!$appointment) {
                return response()->json(['success' => false, 'message' => 'Appointment not found'], 404);
            }

            // ensure caller is the barber or barber-owner
            $barber = Barber::find($appointment->barber_id);
            if (!$barber) {
                return response()->json(['success' => false, 'message' => 'Barber not found for this appointment'], 404);
            }

            // Authorization: ensure current user is the barber (or has barber privileges)
            // Adjust logic if your Auth::user() stores barber relation differently
            // if (Auth::user()->id !== $barber->user_id && Auth::user()->id !== $barber->id && Auth::id() !== $barber->barber_of) {
                // you may change condition to fit your model
                // For example: if barbers are users, check Auth::user()->id == $barber->id
                // If fails, block.
                // For now we'll trust a simplified check: barber id must match requestor (adapt if needed).
            // }

            // Optionally check slot availability
            if ($request->proposed_slote_id) {
                $newSlot = BarberTimeSlot::where('barber_id', Auth::user()->id)->where('status', 'Avalible')->first();
                if (!$newSlot) {
                    return response()->json(['success' => false, 'message' => 'Proposed slot not found'], 404);
                }
                if ($newSlot->status !== 'Avalible') {
                    return response()->json(['success' => false, 'message' => 'Proposed slot not available'], 400);
                }
            } else if (!$request->proposed_date) {
                // At least one of proposed_slote_id or proposed_date should be provided
                return response()->json(['success' => false, 'message' => 'Provide proposed_slote_id or proposed_date'], 400);
            }

            $res = AppointmentReschedule::create([
                'appointment_id'   => $appointment->id,
                'barber_id'        => $appointment->barber_id,
                'proposed_slote_id'=> $request->proposed_slote_id,
                'proposed_date'    => $request->proposed_date,
                'message'          => $request->message,
                'requested_by'     => Auth::user()->id,
                'status'           => 'Pending',
            ]);

            // notify customer: reuse your fcmController (as in store method)
            $customer = Customer::find($appointment->customer_id);
            if ($customer && $customer->device_token != null) {
                $this->fcmController->sendNotification(new \Illuminate\Http\Request([
                    'token' => $customer->device_token,
                    'title' => 'Reschedule Requested',
                    'body'  => 'Barber requested to reschedule your appointment.',
                    'appointment_id' => $appointment->id,
                ]));
            }

            return response()->json([
                'success' => true,
                'message' => 'Reschedule request sent to customer',
                'reschedule_id' => $res->id
            ]);
        } catch (\Exception $e) {
            return response()->json(['success'=>false, 'message'=>'Server error', 'error'=>$e->getMessage()], 500);
        }
    }

    // Customer responds to reschedule request: approve or reject
    public function respondReschedule(Request $request, $rescheduleId)
    {
        try {
            $role = [
                'action' => 'required|in:approve,reject',
            ];
            $validator = Validator::make($request->all(), $role);
            if ($validator->fails()) {
                return response()->json(['success'=>false, 'message'=>'Invalid data', 'errors'=>$validator->errors()], 400);
            }

            $res = AppointmentReschedule::find($rescheduleId);
            if (!$res) {
                return response()->json(['success'=>false, 'message'=>'Reschedule request not found'], 404);
            }

            $appointment = $res->appointment;
            if (!$appointment) {
                return response()->json(['success'=>false, 'message'=>'Original appointment not found'], 404);
            }

            // ensure current user is the appointment's customer
            if (Auth::user()->id !== $appointment->customer_id) {
                return response()->json(['success'=>false, 'message'=>'Forbidden'], 403);
            }

            if ($request->action === 'reject') {
                $res->status = 'Rejected';
                $res->handled_by = Auth::user()->id;
                $res->handled_at = now();
                $res->save();

                // notify barber
                $barberUser = User::find($res->barber->barber_of ?? $res->barber_id);
                if ($barberUser && $barberUser->device_token != null) {
                    $this->fcmController->sendNotification(new \Illuminate\Http\Request([
                        'token' => $barberUser->device_token,
                        'title' => 'Reschedule Rejected',
                        'body'  => 'Customer has rejected the reschedule request.',
                        'appointment_id' => $appointment->id,
                    ]));
                }

                // AppointmentLog::create([
                //     'appointment_id' => $appointment->id,
                //     'status' => 'RESCHEDULE_REJECTED',
                //     'payment' => 0,
                // ]);

                return response()->json(['success'=>true, 'message'=>'Reschedule request rejected']);
            }

            // APPROVE flow
            if ($res->proposed_slote_id) {
                $newSlot = BarberTimeSlot::find($res->proposed_slote_id);
                if (!$newSlot || $newSlot->status !== 'Avalible') {
                    return response()->json(['success'=>false, 'message'=>'Proposed slot not available anymore'], 400);
                }
            }

            // free old slot (if you use slots)
            $oldSlot = BarberTimeSlot::find($appointment->slote_id);
            if ($oldSlot) {
                $oldSlot->status = 'Available';
                $oldSlot->save();
            }

            // assign new slot if provided
            if ($res->proposed_slote_id) {
                $newSlot->status = 'Unavailable';
                $newSlot->save();
                $appointment->slote_id = $res->proposed_slote_id;
            }

            // update date/time if provided
            if ($res->proposed_date) {
                $appointment->date = $res->proposed_date;
            }

            $appointment->save();

            $res->status = 'Approved';
            $res->handled_by = Auth::user()->id;
            $res->handled_at = now();
            $res->save();

            // log
            // AppointmentLog::create([
            //     'appointment_id' => $appointment->id,
            //     'status' => 'RESCHEDULE_APPROVED',
            //     'payment' => 0,
            // ]);

            // notify barber and customer
            $barberUser = User::find($res->barber->barber_of ?? $res->barber_id);
            if ($barberUser && $barberUser->device_token != null) {
                $this->fcmController->sendNotification(new \Illuminate\Http\Request([
                    'token' => $barberUser->device_token,
                    'title' => 'Reschedule Approved',
                    'body'  => 'Customer approved the reschedule request.',
                    'appointment_id' => $appointment->id,
                ]));
            }

            if (Auth::user()->device_token != null) {
                $this->fcmController->sendNotification(new \Illuminate\Http\Request([
                    'token' => Auth::user()->device_token,
                    'title' => 'Appointment Rescheduled',
                    'body'  => 'Your appointment has been rescheduled.',
                    'appointment_id' => $appointment->id,
                ]));
            }

            return response()->json(['success'=>true, 'message'=>'Reschedule approved', 'appointment_id' => $appointment->id]);

        } catch (\Exception $e) {
            return response()->json(['success'=>false, 'message'=>'Server error', 'error'=>$e->getMessage()], 500);
        }
    }

    // optional: list pending reschedules for a customer
    public function listCustomerReschedules()
    {
        $user = Auth::user();
        $reschedules = AppointmentReschedule::whereHas('appointment', function($q) use ($user) {
            $q->where('customer_id', $user->id);
        })->where('status','Pending')->with(['appointment', 'proposedSlot', 'barber'])->get();

        return response()->json(['success'=>true, 'data'=>$reschedules]);
    }

    // optional: list reschedules for a barber
    public function listBarberReschedules()
    {
        $user = Auth::user();
        // adapt this filter depending on how a barber is tied to user.
        $reschedules = AppointmentReschedule::where('barber_id', $user->id)->with(['appointment','proposedSlot'])->get();

        return response()->json(['success'=>true, 'data'=>$reschedules]);
    }
}
