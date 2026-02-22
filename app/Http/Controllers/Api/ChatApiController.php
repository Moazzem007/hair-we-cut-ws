<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FcmController;
use App\Models\Appointment;
use App\Models\Barber;
use App\Models\ChatMessage;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ChatApiController extends Controller
{
    public $fcmController;

    public function __construct()
    {
        $this->fcmController = new FcmController();
    }

    public function customerSendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'appointment_id' => 'required|integer|exists:appointments,id',
            'message' => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid data send',
                'errors' => $validator->errors(),
            ], 422);
        }

        $customerId = Auth::id();
        $appointment = Appointment::where('id', $request->appointment_id)
            ->where('customer_id', $customerId)
            ->where('payment_status', 'paid')
            ->first();

        if (!$appointment) {
            return response()->json([
                'success' => false,
                'message' => 'Chat is only available for paid appointments.',
            ], 403);
        }

        $message = ChatMessage::create([
            'appointment_id' => $appointment->id,
            'customer_id' => $appointment->customer_id,
            'barber_id' => $appointment->barber_id,
            'sender_type' => 'customer',
            'sender_id' => $customerId,
            'message' => trim($request->message),
        ]);

        $barber = Barber::find($appointment->barber_id);
        $receiverBarber = Barber::find($barber->barber_of);
        if ($receiverBarber && !empty($receiverBarber->device_token)) {
            $this->fcmController->sendNotification(new Request([
                'token' => $receiverBarber->device_token,
                'title' => 'New Message',
                'body' => 'You have received a new message from your customer.',
                'email' => $receiverBarber->email,
            ]));
        }

        return response()->json([
            'success' => true,
            'message' => 'Message sent',
            'data' => $message,
        ]);
    }

    public function customerMessages($appointmentId)
    {
        $customerId = Auth::id();
        $appointment = Appointment::where('id', $appointmentId)
            ->where('customer_id', $customerId)
            ->where('payment_status', 'paid')
            ->first();

        if (!$appointment) {
            return response()->json([
                'success' => false,
                'message' => 'Chat is only available for paid appointments.',
            ], 403);
        }

        $messages = ChatMessage::where('appointment_id', $appointment->id)
            ->where('customer_id', $appointment->customer_id)
            ->where('barber_id', $appointment->barber_id)
            ->latest('id')
            ->take(50)
            ->get()
            ->sortBy('id')
            ->values();

        return response()->json([
            'success' => true,
            'data' => $messages,
        ]);
    }

    public function barberSendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'appointment_id' => 'required|integer|exists:appointments,id',
            'message' => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid data send',
                'errors' => $validator->errors(),
            ], 422);
        }

        $barberId = Auth::id();
        $appointment = Appointment::where('id', $request->appointment_id)
            ->where('barber_id', $barberId)
            ->where('payment_status', 'paid')
            ->first();

        if (!$appointment) {
            return response()->json([
                'success' => false,
                'message' => 'Chat is only available for paid appointments.',
            ], 403);
        }

        $message = ChatMessage::create([
            'appointment_id' => $appointment->id,
            'customer_id' => $appointment->customer_id,
            'barber_id' => $appointment->barber_id,
            'sender_type' => 'barber',
            'sender_id' => $barberId,
            'message' => trim($request->message),
        ]);

        $receiverCustomer = Customer::find($appointment->customer_id);
        if ($receiverCustomer && !empty($receiverCustomer->device_token)) {
            $this->fcmController->sendNotification(new Request([
                'token' => $receiverCustomer->device_token,
                'title' => 'New Message',
                'body' => 'You have received a new message from your barber.',
                'email' => $receiverCustomer->email,
            ]));
        }

        return response()->json([
            'success' => true,
            'message' => 'Message sent',
            'data' => $message,
        ]);
    }

    public function barberMessages($appointmentId)
    {
        $barberId = Auth::id();
        $appointment = Appointment::where('id', $appointmentId)
            ->where('barber_id', $barberId)
            ->where('payment_status', 'paid')
            ->first();

        if (!$appointment) {
            return response()->json([
                'success' => false,
                'message' => 'Chat is only available for paid appointments.',
            ], 403);
        }

        $messages = ChatMessage::where('appointment_id', $appointment->id)
            ->where('customer_id', $appointment->customer_id)
            ->where('barber_id', $appointment->barber_id)
            ->latest('id')
            ->take(50)
            ->get()
            ->sortBy('id')
            ->values();

        return response()->json([
            'success' => true,
            'data' => $messages,
        ]);
    }
}
