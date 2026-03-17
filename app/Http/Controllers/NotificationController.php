<?php

namespace App\Http\Controllers;

use App\Models\AdminNotification;
use App\Models\User;
use App\Models\Customer;
use App\Models\Barber;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = AdminNotification::orderBy('id', 'desc')->get();
        return view('admin.notifications.index', compact('notifications'));
    }

    public function create()
    {
        return view('admin.notifications.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'message' => 'required',
            'target' => 'required'
        ]);

        AdminNotification::create($request->all());

        return redirect()->route('adminnotifications.index')->with('success', 'Notification entry created.');
    }

    public function send(Request $request)
    {
        $id = $request->id;
        $notification = AdminNotification::findOrFail($id);
        
        // Logic to send push notifications via Firebase or other provider would go here
        // For now, we mark as sent
        
        $notification->update(['sent_at' => now()]);
        
        return response()->json(['success' => true, 'message' => 'Push notifications scheduled to ' . $notification->target]);
    }

    public function destroy($id)
    {
        AdminNotification::findOrFail($id)->delete();
        return redirect()->route('adminnotifications.index')->with('success', 'Notification deleted.');
    }
}
