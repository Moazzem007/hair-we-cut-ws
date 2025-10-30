@extends('admin.barbar.layout')



@section('mainContent')
    <div class="container">
        <div class="row">
            <div class="col-md-2">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Total Appointments</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins">{{ number_format($totalappno) }}</h1>
                        {{-- <div class="stat-percent font-bold text-success">98% <i class="fa fa-bolt"></i></div> --}}
                        <small>Total Appointments</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        {{-- <span class="label label-info pull-right">Annual</span> --}}
                        <h5>Completed</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins">{{ number_format($completed) }}</h1>
                        {{-- <div class="stat-percent font-bold text-info">20% <i class="fa fa-level-up"></i></div> --}}
                        <small>Completed</small>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Appointments</h5>
                    </div>
                    <div class="ibox-content">

                        <div class="row">
                            <div class="col-md-6">
                                <h1 class="no-margins">{{ number_format($Pendding) }}</h1>
                                <div class="font-bold text-success"> <small>Pending Appointments</small></div>
                            </div>
                            <div class="col-md-6">
                                <h1 class="no-margins text-danger">{{ number_format($canceled) }}</h1>
                                <div class="font-bold text-danger"> <small>Cancled Appointments</small></div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Rating</h5>
                        <div class="ibox-tools">
                            <span class="label label-waring">{{ number_format($rating, 1) }}</span>
                            @php
                                $ra = number_format($rating);
                            @endphp
                            @for ($i = 1; $i <= $ra; $i++)
                                <i class="fa fa-star" style="color:gold"></i>
                            @endfor
                        </div>
                    </div>
                    <div class="ibox-content no-padding">

                        <div class="flot-chart m-t-lg" style="height: 55px;text-align:center;">
                            <div class="flot-chart-content" id="flot-chart1"></div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <div>
                            <span class="pull-right text-right">
                                {{-- <small>Average value of sales in the past month in: <strong>United states</strong></small>
                                    <br/>
                                    All sales: 162,862 --}}
                            </span>
                            <h3 class="font-bold no-margins">
                                Account Status
                            </h3>
                            {{-- <small>Sales marketing.</small> --}}
                        </div>

                        <div class="m-t-sm">

                            <div class="row">
                                <div class="col-md-8">
                                    <div>
                                        <canvas id="lineChart" height="114"></canvas>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <ul class="stat-list m-t-lg">
                                        <li>
                                            <h2 class="no-margins">2,346</h2>
                                            <small>Total Appointments in period</small>
                                            <div class="progress progress-mini">
                                                <div class="progress-bar" style="width: 48%;"></div>
                                            </div>
                                        </li>
                                        <li>
                                            <h2 class="no-margins ">4,422</h2>
                                            <small>Appointments in last month</small>
                                            <div class="progress progress-mini">
                                                <div class="progress-bar" style="width: 60%;"></div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                        </div>

                        <div class="m-t-md">
                            <small class="pull-right">
                                {{-- <i class="fa fa-clock-o"> </i>
                                Update on 16.07.2015 --}}
                            </small>
                            <small>
                                {{-- <strong>Analysis of sales:</strong> The value has been changed over time, and last month reached a level over $50,000. --}}
                            </small>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        {{-- <span class="label label-warning pull-right">Data has changed</span> --}}
                        <h5>Recently Completed Appointments</h5>
                    </div>
                    <div class="ibox-content" style="height: 220px; overflow:scroll;">


                        <div class="row">

                            <div class="col-md-12">
                                <table class="table table-striped">
                                    <tbody>
                                        @foreach ($app_amounts as $key => $appamount)
                                            <tr>
                                                <td>{{ $appamount->date }}</td>
                                                <td>{{ $appamount->customer->name }}</td>
                                                <td>
                                                    @if ($appamount->wallet != null)
                                                        {{ $appamount->wallet->Total }}
                                                    @endif
                                                </td>
                                                <td>{{ $appamount->status }}</td>
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

        <div class="row">

            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>New Appointments </h5>
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
                            <table class="table table-striped table-bordered table-hover dataTables-example">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Contact</th>
                                        <th>Address</th>
                                        <th>Service </th>
                                        <th>Type </th>
                                        <th>Date </th>
                                        <th>Barber #</th>
                                        <th>Timing </th>
                                        <th>Service Type</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($totalapp as $key => $app)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $app->customer->name }}</td>
                                            <td>{{ $app->customer->contact }}</td>
                                            <td>{{ $app->address }}</td>
                                            <td>{{ $app->service?->title ?? 'No Service' }}</td>
                                            <td>{{ $app->service_type }}</td>
                                            <td>{{ $app->date }}</td>
                                            <td>
                                                @if ($app->slot != null)
                                                    {{ $app->slot->slot_no }}
                                                @endif
                                            </td>
                                            <td>
                                                @if ($app->slot != null)
                                                    {{ $app->slot->from_time }} To {{ $app->slot->to_time }}
                                                @endif
                                            </td>
                                            <td>{{ $app->appType }}</td>
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
