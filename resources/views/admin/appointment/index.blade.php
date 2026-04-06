@extends('layouts.adminapp')

@section('Main-content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Appointments Management</h2>
            <ol class="breadcrumb">
                <li><a href="{{ route('adminDashboard') }}">Home</a></li>
                <li class="active"><strong>Appointments</strong></li>
            </ol>
        </div>
    </div>

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content" style="border-radius: 12px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.05);">
                        <div class="table-responsive">
                            <table class="table table-hover dataTables-example">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Barber / Salon</th>
                                        <th>Customer</th>
                                        <th>Schedule</th>
                                        <th>Status</th>
                                        <th>Service Details</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($appointments as $key => $appointment)
                                        <tr>
                                            <td>#{{ $appointment->id }}</td>
                                            <td>
                                                <strong>{{ $appointment->barber ? $appointment->barber->name : 'N/A' }}</strong><br>
                                                <small class="text-muted">{{ $appointment->barber ? $appointment->barber->salon : '' }}</small>
                                            </td>
                                            <td>
                                                <strong>{{ optional($appointment->customer)->name ?? 'Unknown Customer' }}</strong><br>
                                                <small class="text-muted">{{ optional($appointment->customer)->contact ?? 'No Contact' }}</small>
                                            </td>
                                            <td>
                                                <span class="label label-white"><i class="fa fa-calendar"></i> {{ $appointment->date }}</span><br>
                                                <small class="text-navy"><i class="fa fa-clock-o"></i> {{ $appointment->slot ? $appointment->slot->from_time : '' }}</small>
                                            </td>
                                            <td>
                                                @php
                                                    $statusClass = 'label-default';
                                                    if($appointment->status == 'Completed') $statusClass = 'label-primary';
                                                    if($appointment->status == 'Canceled') $statusClass = 'label-danger';
                                                    if($appointment->status == 'Confirmed') $statusClass = 'label-info';
                                                @endphp
                                                <span class="label {{ $statusClass }}">{{ $appointment->status }}</span>
                                            </td>
                                            <td>
                                                <div class="well well-sm">
                                                    <strong>Service:</strong> {{ @$appointment->service->title }}<br>
                                                    <strong>Type:</strong> {{ $appointment->appType }}<br>
                                                    <strong>Reason:</strong> {{ $appointment->reason ? $appointment->reason->reason : 'None' }}
                                                </div>
                                            </td>
                                            <td class="text-right">
                                                <div class="btn-group">
                                                    <button class="btn btn-xs btn-white dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <i class="fa fa-cog m-r-xs"></i> <i class="fa fa-angle-down"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-right">
                                                        <li><a href="{{ route('appointment.show', $appointment->id) }}" style="padding: 8px 15px;"><i class="fa fa-eye m-r-xs"></i> View Details</a></li>
                                                        
                                                        <li class="divider"></li>
                                                        <li class="dropdown-header">Contact Customer</li>
                                                        @if($appointment->customer && $appointment->customer->contact)
                                                            <li><a href="tel:{{ $appointment->customer->contact }}" style="padding: 8px 15px;"><i class="fa fa-phone m-r-xs text-primary"></i> Call Customer</a></li>
                                                            <li><a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $appointment->customer->contact) }}" target="_blank" style="padding: 8px 15px;"><i class="fa fa-whatsapp m-r-xs text-success"></i> WhatsApp</a></li>
                                                        @else
                                                            <li><span style="padding: 8px 15px; color: #999;"><i class="fa fa-phone m-r-xs"></i> No Contact Info</span></li>
                                                        @endif
                                                        
                                                        <li class="divider"></li>
                                                        <li class="dropdown-header">Manage Status</li>
                                                        @if ($appointment->status == 'Canceled' && !$appointment->refund)
                                                            <li><a href="{{ route('refundPayment', $appointment->id) }}" onclick="return confirm('Process refund for this appointment?')" style="padding: 8px 15px; color: #f59e0b;"><i class="fa fa-money m-r-xs"></i> Process Refund</a></li>
                                                        @endif
                                                        
                                                        @if ($appointment->status != 'Completed' && $appointment->status != 'Canceled')
                                                            <li><a href="{{ route('completedStatus', $appointment->id) }}" onclick="return confirm('Mark as completed?')" style="padding: 8px 15px; color: #10b981;"><i class="fa fa-check m-r-xs"></i> Mark Completed</a></li>
                                                        @endif
                                                        
                                                        <li><a href="{{ route('appointmentDelete', $appointment->id) }}" class="text-danger" style="padding: 8px 15px;" onclick="return confirm('Attention: This will permanently delete this appointment record. Continue?')"><i class="fa fa-trash m-r-xs"></i> Delete Record</a></li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
