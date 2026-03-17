@extends('layouts.adminapp')

@section('Main-content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Barber & Salon Management</h2>
            <ol class="breadcrumb">
                <li><a href="{{ route('adminDashboard') }}">Home</a></li>
                <li class="active"><strong>Barbers</strong></li>
            </ol>
        </div>
    </div>

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row m-b-md">
            <div class="col-lg-12">
                <div class="btn-group">
                    <a href="{{ route('barbers.index', ['status' => 'Active']) }}" class="btn btn-sm btn-white @if($status == 'Active') active @endif">Active Partners</a>
                    <a href="{{ route('barbers.index', ['status' => 'Pending']) }}" class="btn btn-sm btn-white @if($status == 'Pending' || $status == 'Pendding') active @endif">
                        Pending Approvals 
                        <span class="label label-warning">{{ App\Models\Barber::where('status', 'Pending')->orWhere('status', 'Pendding')->count() }}</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content" style="border-radius: 12px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.05);">
                        <div class="table-responsive">
                            <table class="table table-hover dataTables-example" style="width: 100%; margin-bottom: 0;">
                                <thead>
                                    <tr>
                                        <th style="width: 25%;">Partner</th>
                                        <th style="width: 10%;">Role</th>
                                        <th style="width: 20%;">Shop / Affiliation</th>
                                        <th style="width: 15%;">Contact</th>
                                        <th style="width: 15%;">Performance</th>
                                        <th style="width: 10%;">Status</th>
                                        <th class="text-right" style="width: 5%;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($barbers as $barbar)
                                        <tr>
                                            <td>
                                                <div style="display: flex; align-items: center;">
                                                    <div style="margin-right: 12px;">
                                                        <span class="img-circle bg-primary" style="width: 38px; height: 38px; color: white; font-weight: bold; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, var(--primary) 0%, #4f46e5 100%);">
                                                            {{ substr($barbar->name, 0, 1) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <strong style="color: var(--secondary);">{{ $barbar->name }}</strong><br>
                                                        <small class="text-muted">{{ $barbar->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($barbar->barber_of == null)
                                                    <span class="label" style="background: rgba(99, 102, 241, 0.1); color: var(--primary); border: 1px solid rgba(99, 102, 241, 0.2); font-weight: 600;">Salon</span>
                                                @else
                                                    <span class="label label-info" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.2); font-weight: 600;">Barber</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($barbar->barber_of == null)
                                                    <span style="font-weight: 500;">{{ $barbar->salon }}</span>
                                                @else
                                                    <div class="text-muted" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Works at:</div>
                                                    <strong style="font-weight: 600;">{{ $barbar->salonOwner ? $barbar->salonOwner->salon : 'Independent' }}</strong>
                                                @endif
                                            </td>
                                            <td style="font-family: monospace; font-size: 13px;">{{ $barbar->contact }}</td>
                                            <td>
                                                <div style="display: flex; gap: 15px;">
                                                    <div>
                                                        <small class="text-muted" style="display: block; font-size: 10px; text-transform: uppercase;">Rating</small>
                                                        <strong style="color: #f59e0b;"><i class="fa fa-star"></i> {{ $barbar->rating->isNotEmpty() ? number_format($barbar->rating[0]->rate, 1) : 'N/A' }}</strong>
                                                    </div>
                                                    <div style="border-left: 1px solid #eee; padding-left: 15px;">
                                                        <small class="text-muted" style="display: block; font-size: 10px; text-transform: uppercase;">Orders</small>
                                                        <strong style="color: var(--secondary);">{{ $barbar->appoitment->isNotEmpty() ? $barbar->appoitment[0]->appointments : 0 }}</strong>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if ($barbar->status == 'Active')
                                                    <span class="label" style="background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2); font-weight: 600;">Approved</span>
                                                @else
                                                    <span class="label" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.2); font-weight: 600;">Pending</span>
                                                @endif
                                            </td>
                                            <td class="text-right">
                                                <div style="display: flex; justify-content: flex-end; gap: 5px; align-items: center;">
                                                    @if($barbar->status != 'Active')
                                                        <a href="{{ route('barberactivestatus', $barbar->id) }}" class="btn btn-xs btn-primary" style="background: var(--primary); border: none; border-radius: 4px;"><i class="fa fa-check"></i></a>
                                                    @endif
                                                    <div class="btn-group">
                                                        <button class="btn btn-xs btn-white dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="border: 1px solid #e2e8f0; border-radius: 4px; padding: 2px 8px;">
                                                            <i class="fa fa-ellipsis-v"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-right">
                                                            <li><a href="{{ route('barberprofileadmin', $barbar->user_id ?: $barbar->id) }}" style="padding: 8px 15px;"><i class="fa fa-user m-r-xs"></i> View Profile</a></li>
                                                            <li><a href="{{ route('barberappointmenthistory', $barbar->user_id ?: $barbar->id) }}" style="padding: 8px 15px;"><i class="fa fa-calendar m-r-xs"></i> Appointments</a></li>
                                                            <li><a href="{{ route('barberwallethistory', $barbar->user_id ?: $barbar->id) }}" style="padding: 8px 15px;"><i class="fa fa-wallet m-r-xs"></i> Wallet History</a></li>
                                                            
                                                            <li class="divider" style="margin: 5px 0;"></li>
                                                            <li class="dropdown-header" style="font-size: 10px; text-transform: uppercase; color: #94a3b8; padding: 5px 15px;">Account Control</li>
                                                            
                                                            @if($barbar->status == 1)
                                                                <li><a href="{{ route('disabledstatus', $barbar->id) }}" style="padding: 8px 15px; color: #f59e0b;" onclick="return confirm('Disable this partner account?')"><i class="fa fa-ban m-r-xs"></i> Disable Account</a></li>
                                                            @else
                                                                <li><a href="{{ route('activestatus', $barbar->id) }}" style="padding: 8px 15px; color: #10b981;" onclick="return confirm('Enable this partner account?')"><i class="fa fa-check-circle m-r-xs"></i> Enable Account</a></li>
                                                            @endif
                                                            
                                                            <li><a href="{{ route('barbarDelete', $barbar->id) }}" class="text-danger" style="padding: 8px 15px;" onclick="return confirm('Attention: This will permanently delete this partner and all related data. Proceed?')"><i class="fa fa-trash m-r-xs"></i> Delete Partner</a></li>
                                                        </ul>
                                                    </div>
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
