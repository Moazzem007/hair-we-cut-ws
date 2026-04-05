<?php

use App\Models\Barber;
use App\Models\Wallet;
use App\Models\PayoutRequest;
use Carbon\Carbon;

// 1. Ensure we have a barber
$barber = Barber::first();
if (!$barber) {
    echo "No barber found. Please create a barber first.\n";
    exit;
}

// 2. Add some UNPAID wallet entries simulating appointments
$mixid = Wallet::max('inv') ?? 0;

// Appointment 1: £30
Wallet::create([
    'user_id' => 0,
    'barber_id' => $barber->id,
    'salon_id' => 0,
    'appointment_id' => 0,
    'inv' => ++$mixid,
    'debit' => 24.00, // Partner's 80% share
    'credit' => 0,
    'com_amount' => 6.00, // Platform's 20%
    'pay_status' => 'UNPAID',
    'description' => 'Test Booking Payment (£30)'
]);

// Appointment 2: £60
Wallet::create([
    'user_id' => 0,
    'barber_id' => $barber->id,
    'salon_id' => 0,
    'appointment_id' => 0,
    'inv' => ++$mixid,
    'debit' => 48.00, // Partner's 80% share
    'credit' => 0,
    'com_amount' => 12.00, // Platform's 20%
    'pay_status' => 'UNPAID',
    'description' => 'Test Booking Payment (£60)'
]);

// Appointment 3: £100
Wallet::create([
    'user_id' => 0,
    'barber_id' => $barber->id,
    'salon_id' => 0,
    'appointment_id' => 0,
    'inv' => ++$mixid,
    'debit' => 80.00, 
    'credit' => 0,
    'com_amount' => 20.00,
    'pay_status' => 'UNPAID',
    'description' => 'Test Booking Payment (£100)'
]);


// 3. Create some Payout Requests
// A pending one
PayoutRequest::create([
    'barber_id' => $barber->id,
    'amount' => 50.00,
    'status' => 'pending',
    'created_at' => Carbon::now()->subDays(1),
    'updated_at' => Carbon::now()->subDays(1)
]);

// An approved one
PayoutRequest::create([
    'barber_id' => $barber->id,
    'amount' => 60.00,
    'status' => 'approved',
    'created_at' => Carbon::now()->subDays(5),
    'updated_at' => Carbon::now()->subDays(4)
]);

// A rejected one
PayoutRequest::create([
    'barber_id' => $barber->id,
    'amount' => 100.00,
    'status' => 'rejected',
    'admin_note' => 'Invalid banking details provided.',
    'created_at' => Carbon::now()->subDays(10),
    'updated_at' => Carbon::now()->subDays(9)
]);

echo "Test data for Payout system inserted successfully!\n";
