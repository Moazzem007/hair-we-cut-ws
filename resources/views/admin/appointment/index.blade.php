@extends('layouts.adminapp')




@section('Main-content')
    {{-- Page Header --}}
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Appointment List</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ route('adminDashboard') }}">Home</a>
                </li>
                <li>
                    <a>Appointment</a>
                </li>
                <li class="active">
                    <strong>Appointment List</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">

        </div>
    </div>

    {{-- Page Header  End --}}


    {{-- Main Body --}}

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        {{-- <h5>Basic Data Tables example with responsive plugin</h5> --}}
                        {{-- <a data-toggle="modal" class="btn btn-primary" href="#modal-form">Add barbar</a> --}}
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                <i class="fa fa-wrench"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-user">
                                <li><a href="#">Config option 1</a>
                                </li>
                                <li><a href="#">Config option 2</a>
                                </li>
                            </ul>
                            <a class="close-link">
                                <i class="fa fa-times"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table
                                class="table table-striped table-bordered table-hover dataTables-example toggle-arrow-tiny footable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Barber </th>
                                        <th>Customer</th>
                                        <th>Slot #</th>
                                        <th>Time</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Reason</th>
                                        <th data-toggle="true">Services</th>
                                        <th data-hide="all">Servies Details</th>
                                        <th data-hide="all">Service Type</th>
                                        <th data-hide="all">Logs</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($appointments as $key => $appointment)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                          <td>
    {{ $appointment->barber ? $appointment->barber->name : 'No barber assigned' }}
</td>

                                            <td>{{ $appointment->customer->name }}</td>
                                            <td>
                                                {{ $appointment->slot ? $appointment->slot->slot_no : '' }}
                                            </td>
                                            <td>
                                                {{ $appointment->slot ? $appointment->slot->from_time : '' }} /
                                                {{ $appointment->slot ? $appointment->slot->to_time : '' }}
                                            </td>
                                            <td>{{ $appointment->date }}</td>
                                            <td>

                                                {{ $appointment->status }}

                                                @if ($appointment->status == 'Canceled' && !$appointment->refund)
                                                    <a class="btn btn-info btn-xs"
                                                        href="{{ route('refundPayment', $appointment->id) }}">Refund</a>
                                                @endif

                                            </td>
                                            <td>
                                                @if ($appointment->status == 'Canceled')
                                                    @if ($appointment->reason != null)
                                                        {{ $appointment->reason->reason }}
                                                    @endif
                                                @endif

                                                @if ($appointment->status == 'Review')
                                                    @for ($i = 0; $i < $appointment->rating->rating; $i++)
                                                        <i class="fa fa-star" style="color:gold;"></i>
                                                    @endfor
                                                @endif
                                            </td>
                                            <td>Details</td>
                                            <td>{{ @$appointment->service->title }}</td>
                                            <td>{{ $appointment->appType }}</td>
                                            <td>
                                                <table class="table">
                                                    <tbody>
                                                        <tr>
                                                            <th>Status</th>
                                                            <th>Payment</th>
                                                            <th>Date</th>
                                                        </tr>
                                                        @if ($appointment->log->isNotEmpty())
                                                            @foreach ($appointment->log as $log)
                                                                <tr>
                                                                    <td>{{ $log->status }}</td>
                                                                    <td>{{ $log->payment }}</td>
                                                                    <td>{{ $log->created_at->format('Y-m-d') }}</td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                    </tbody>
                                                </table>
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





    {{-- Main Body End --}}
@endsection
