@extends('layouts.adminapp')




@section('Main-content')
    {{-- Page Header --}}
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Barber List</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ route('adminDashboard') }}">Home</a>
                </li>
                <li>
                    <a>Barbers</a>
                </li>
                <li class="active">
                    <strong>Barber List</strong>
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
                                        <th>Full Name</th>
                                        <th>Mobile</th>
                                        <th>Shop Name</th>
                                        <th>address</th>
                                        <th>Rating</th>
                                        <th>Revenue</th>
                                        <th>Appointments</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($barbers as $key => $barbar)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $barbar->name }} </td>
                                            <td>{{ $barbar->contact }}</td>
                                            <td>{{ $barbar->salon }}</td>
                                            <td width="200">{{ $barbar->address }}</td>
                                            <td>
                                                @if ($barbar->rating->isNotEmpty())
                                                    <h4 style="text-align:center;">
                                                        {{ number_format($barbar->rating[0]->reviews, 2) }}
                                                        <br />
                                                        <small>Reviews({{ $barbar->rating[0]->reviews }})</small>
                                                    </h4>
                                                @else
                                                    0
                                                @endif
                                            </td>

                                            <td>
                                                @if ($barbar->wallet->isNotEmpty())
                                                    <h4 style="text-align:center;">
                                                        <i class="fa fa-gbp"></i>
                                                        {{ number_format($barbar->wallet[0]->total, 2) }}
                                                    </h4>
                                                @else
                                                    0
                                                @endif
                                            </td>
                                            <td>
                                                @if ($barbar->appoitment->isNotEmpty())
                                                    <h4 style="text-align:center;">
                                                        {{ number_format($barbar->appoitment[0]->appointments) }}
                                                    </h4>
                                                @else
                                                    0
                                                @endif
                                            </td>
                                            <td>
                                                @if ($barbar->status == 'Active')
                                                    <span class="label" style='background:#50af53;color:#fff;'><i
                                                            class="fa fa-check"></i>Approved</span>
                                                @elseif($barbar->status == 'Pendding')
                                                    <span class="label label-success">Pending</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">

                                                    <a href="{{ route('barberprofileadmin', $barbar->user_id) }}"
                                                        class="btn btn-xs btn-outline btn-success"><i
                                                            class="fa fa-user"></i></a>
                                                    <a href="{{ route('barberappointmenthistory', $barbar->user_id) }}"
                                                        class="btn btn-xs btn-outline btn-success"><i
                                                            class="fa fa-book"></i></a>
                                                    <a href="{{ route('barberwallethistory', $barbar->user_id) }}"
                                                        class="btn btn-xs btn-outline btn-success"><i
                                                            class="fa fa-google-wallet"></i></a>

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





    {{-- Main Body End --}}
@endsection
