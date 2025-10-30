@extends('layouts.adminapp')




@section('Main-content')
    <style>
        .sidebar-content .wrapper,
        .wrapper.sidebar-content {
            padding-right: 230px !important;
        }
    </style>

    <div class="sidebar-panel">
        <div>
            <h4>Messages <span
                    class="badge badge-info pull-right">{{ App\Models\Contactus::where('status', 1)->count() }}</span></h4>
            @foreach ($contactus as $message)
                <div class="feed-element" style="background-color: #fff;padding:5px;border-radius:10px;">
                    <a href="#" class="pull-left">

                        <h5><span class=" fa fa-envelope"></span> &nbsp; {{ $message->name }}</h5>
                    </a>
                    <div class="media-body">
                        <a href="">{{ $message->email }}</a>
                        <p>{{ $message->message }}</p>
                        <small class="text-muted">Today 4:21 pm</small>
                    </div>
                </div>
            @endforeach

        </div>

    </div>
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <div>
                            <span class="pull-right text-right">
                                <small>Hair We Cut Commission: <i class="fa fa-gbp"></i> <strong>
                                        {{ number_format($comission, 2) }}</strong></small>
                                <br />

                            </span>
                            <h1 class="m-b-xs"><i class="fa fa-gbp"></i> {{ number_format($wallet, 2) }}</h1>
                            <h3 class="font-bold no-margins">
                                Total Revenue
                            </h3>
                            <small>Sales marketing.</small>
                        </div>

                        <div>
                            <canvas id="lineChart" height="70"></canvas>
                        </div>

                        <div class="m-t-md">
                            <small class="pull-right">
                                <i class="fa fa-clock-o"> </i>
                                Update on 16.07.2015
                            </small>
                            <small>
                                <strong>Analysis of sales:</strong> The value has been changed over time, and last month
                                reached a level over $50,000.
                            </small>
                        </div>

                    </div>
                </div>
            </div>
        </div>


        <div class="row">

            <div class="col-lg-4">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        {{-- <span class="label label-primary pull-right">Today</span> --}}
                        <h5>Barbers</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins">{{ number_format($barbers) }}</h1>
                        {{-- <div class="stat-percent font-bold text-navy">20% <i class="fa fa-level-up"></i></div> --}}
                        <small></small>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        {{-- <span class="label label-info pull-right">Monthly</span> --}}
                        <h5>Customers</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins">{{ number_format($customers) }}</h1>
                        {{-- <div class="stat-percent font-bold text-info">40% <i class="fa fa-level-up"></i></div> --}}
                        <small></small>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        {{-- <span class="label label-warning pull-right">Today</span> --}}
                        <h5>Appointments</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins">{{ number_format($appoitments) }}</h1>
                        {{-- <div class="stat-percent font-bold text-warning">16% <i class="fa fa-level-up"></i></div> --}}
                        <small></small>
                    </div>
                </div>
            </div>
        </div>


    </div>
@endsection
