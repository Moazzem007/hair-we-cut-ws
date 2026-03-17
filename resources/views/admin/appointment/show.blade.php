@extends('layouts.adminapp')

@section('Main-content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Appointment Details</h2>
            <ol class="breadcrumb">
                <li><a href="{{ route('adminDashboard') }}">Home</a></li>
                <li><a href="{{ route('appointment.index') }}">Appointments</a></li>
                <li class="active"><strong>#{{ $appointment->id }}</strong></li>
            </ol>
        </div>
        <div class="col-lg-2 text-right">
            <div class="m-t-md">
                <a href="{{ route('appointment.index') }}" class="btn btn-white btn-sm"><i class="fa fa-arrow-left"></i> Back to List</a>
            </div>
        </div>
    </div>

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-8">
                <div class="ibox float-e-margins">
                    <div class="ibox-title" style="background: transparent; border: none; padding: 20px 25px;">
                        <h5 style="font-weight: 700; color: var(--secondary);">Service Information</h5>
                        <div class="ibox-tools">
                            @php
                                $statusClass = 'label-default';
                                if($appointment->status == 'Completed') $statusClass = 'label-primary';
                                if($appointment->status == 'Canceled') $statusClass = 'label-danger';
                                if($appointment->status == 'Confirmed') $statusClass = 'label-info';
                                if($appointment->status == 'Pending') $statusClass = 'label-warning';
                            @endphp
                            <span class="label {{ $statusClass }} p-xs" style="font-size: 11px; padding: 5px 12px; border-radius: 20px;">{{ strtoupper($appointment->status) }}</span>
                        </div>
                    </div>
                    <div class="ibox-content" style="border-radius: 12px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.05); padding: 30px;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="m-b-md">
                                    <small class="text-muted" style="text-transform: uppercase; font-weight: 700; letter-spacing: 1px;">Selected Service</small>
                                    <h2 style="margin-top: 5px; color: var(--primary); font-weight: 700;">{{ $appointment->service ? $appointment->service->title : 'N/A' }}</h2>
                                    <p class="text-muted"><i class="fa fa-tag m-r-xs"></i> {{ $appointment->appType }}</p>
                                </div>
                            </div>
                            <div class="col-md-6 text-right">
                                <div class="m-b-md">
                                    <small class="text-muted" style="text-transform: uppercase; font-weight: 700; letter-spacing: 1px;">Appointment Fee</small>
                                    <h2 style="margin-top: 5px; font-weight: 700;">£{{ number_format($appointment->service ? $appointment->service->price : 0, 2) }}</h2>
                                    @if($appointment->payment_status == 'paid')
                                        <span class="text-success" style="font-weight: 600;"><i class="fa fa-check-circle"></i> Payment Received</span>
                                    @else
                                        <span class="text-warning" style="font-weight: 600;"><i class="fa fa-clock-o"></i> Payment Pending</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <hr style="border-top: 1px solid #f1f5f9; margin: 25px 0;">

                        <div class="row">
                            <div class="col-md-6">
                                <h4 style="font-weight: 700; margin-bottom: 15px;">Schedule Detail</h4>
                                <ul class="list-unstyled" style="line-height: 2.2;">
                                    <li><i class="fa fa-calendar m-r-xs text-primary"></i> <span class="text-muted">Date:</span> <strong>{{ \Carbon\Carbon::parse($appointment->date)->format('D, M d, Y') }}</strong></li>
                                    <li><i class="fa fa-clock-o m-r-xs text-primary"></i> <span class="text-muted">Time:</span> <strong>{{ $appointment->slot ? $appointment->slot->from_time : 'N/A' }}</strong></li>
                                    <li><i class="fa fa-history m-r-xs text-primary"></i> <span class="text-muted">Booked:</span> <strong>{{ $appointment->created_at->diffForHumans() }}</strong></li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h4 style="font-weight: 700; margin-bottom: 15px;">Location Info</h4>
                                @if($appointment->appType == 'Mobile_shop')
                                    <p style="line-height: 1.6;">
                                        <strong><i class="fa fa-map-marker text-danger"></i> Mobile Service Address:</strong><br>
                                        {{ $appointment->address }}<br>
                                        {{ $appointment->address2 ? $appointment->address2 . ',' : '' }} {{ $appointment->town }}<br>
                                        {{ $appointment->postcode }}
                                    </p>
                                @else
                                    <p style="line-height: 1.6;">
                                        <strong><i class="fa fa-building text-info"></i> Salon Service:</strong><br>
                                        {{ $appointment->barber ? $appointment->barber->salon : 'N/A' }}<br>
                                        {{ $appointment->barber ? $appointment->barber->address : '' }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ibox float-e-margins m-t-lg">
                    <div class="ibox-title" style="background: transparent; border: none; padding: 20px 25px;">
                        <h5 style="font-weight: 700; color: var(--secondary);">Status Logs</h5>
                    </div>
                    <div class="ibox-content" style="border-radius: 12px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.05); padding: 0;">
                        <table class="table table-hover no-margins">
                            <thead>
                                <tr style="background: #f8fafc;">
                                    <th style="padding: 15px 25px; border: none;">Action</th>
                                    <th style="padding: 15px 25px; border: none;">Date & Time</th>
                                    <th style="padding: 15px 25px; border: none;" class="text-right">Reference</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($appointment->log as $log)
                                    <tr>
                                        <td style="padding: 15px 25px;">
                                            <span class="label label-white" style="border: 1px solid #e2e8f0;">{{ $log->status }}</span>
                                        </td>
                                        <td style="padding: 15px 25px;">{{ $log->created_at->format('M d, Y H:i') }}</td>
                                        <td style="padding: 15px 25px;" class="text-right text-muted">#{{ $log->id }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center p-md">No logs found for this appointment.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="ibox float-e-margins">
                    <div class="ibox-title" style="background: transparent; border: none; padding: 20px 25px;">
                        <h5 style="font-weight: 700; color: var(--secondary);">Customer Profile</h5>
                    </div>
                    <div class="ibox-content" style="border-radius: 12px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.05); text-align: center; padding: 40px 30px;">
                        <div class="m-b-md">
                            <img alt="image" class="img-circle" src="https://ui-avatars.com/api/?name={{ urlencode($appointment->customer->name) }}&background=6366f1&color=fff" style="width: 80px; height: 80px; margin-bottom: 20px;">
                            <h3 style="font-weight: 700; margin-bottom: 5px;">{{ $appointment->customer->name }}</h3>
                            <p class="text-muted">Member since {{ $appointment->customer->created_at->format('M Y') }}</p>
                        </div>
                        <div class="text-left m-t-md" style="background: #f8fafc; padding: 20px; border-radius: 8px;">
                            <p style="margin-bottom: 10px;"><i class="fa fa-envelope m-r-xs text-primary"></i> {{ $appointment->customer->email }}</p>
                            <p style="margin-bottom: 0;"><i class="fa fa-phone m-r-xs text-primary"></i> {{ $appointment->customer->contact }}</p>
                        </div>
                        <div class="m-t-md">
                            <a href="tel:{{ $appointment->customer->contact }}" class="btn btn-outline btn-primary btn-block" style="border-radius: 8px;"><i class="fa fa-phone"></i> Call Customer</a>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $appointment->customer->contact) }}" target="_blank" class="btn btn-outline btn-success btn-block" style="border-radius: 8px;"><i class="fa fa-whatsapp"></i> WhatsApp</a>
                        </div>
                    </div>
                </div>

                <div class="ibox float-e-margins m-t-lg">
                    <div class="ibox-title" style="background: transparent; border: none; padding: 20px 25px;">
                        <h5 style="font-weight: 700; color: var(--secondary);">Partner Info</h5>
                    </div>
                    <div class="ibox-content" style="border-radius: 12px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.05); padding: 30px;">
                        <h4 style="font-weight: 700; color: var(--primary);">{{ $appointment->barber ? $appointment->barber->name : 'N/A' }}</h4>
                        <p class="text-muted"><i class="fa fa-scissors"></i> {{ $appointment->barber ? $appointment->barber->salon : 'N/A' }}</p>
                        <hr style="border-top: 1px solid #f1f5f9;">
                        <div class="m-t-md">
                            <a href="{{ route('barberprofileadmin', $appointment->barber ? ($appointment->barber->user_id ?: $appointment->barber->id) : '#') }}" class="btn btn-white btn-block" style="border-radius: 8px;">View Partner Profile</a>
                        </div>
                    </div>
                </div>

                <div class="ibox float-e-margins m-t-lg">
                    <div class="ibox-content" style="border-radius: 12px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.05); padding: 30px;">
                        <h4 style="font-weight: 700; margin-bottom: 20px;">Admin Actions</h4>
                        
                        @if ($appointment->status == 'Canceled' && !$appointment->refund)
                            <a class="btn btn-warning btn-block" href="{{ route('refundPayment', $appointment->id) }}" onclick="return confirm('Are you sure you want to process refund?')" style="border-radius: 8px; padding: 10px;">Process Refund</a>
                        @endif

                        @if ($appointment->status != 'Completed' && $appointment->status != 'Canceled')
                            <a class="btn btn-primary btn-block" href="{{ route('completedStatus', $appointment->id) }}" onclick="return confirm('Mark this appointment as completed?')" style="border-radius: 8px; padding: 10px;">Mark as Completed</a>
                        @endif

                        <button class="btn btn-danger btn-block m-t-sm" onclick="return confirm('Warning: This will permanently delete the record. Continue?')" style="border-radius: 8px; padding: 10px; opacity: 0.8;">Delete Record</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
