@extends('admin.barbar.layout')



@section('mainContent')
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
    </div>

    {{-- Page Header  End --}}


    {{-- Main Body --}}

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
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
                                    <th>#</th>
                                    <th>Barber Name</th>
                                    <th>Name</th>
                                    <th>Contact</th>
                                    <th>Address</th>
                                    <th>Service Type </th>
                                    <th>Service </th>
                                    <th>Type </th>
                                    <th>Date </th>
                                    <th>Timing </th>
                                    <th>Status</th>
                                    <th>Reason / Rating</th>
                                    <th>Action</th>
                                </thead>
                                <tbody>
                                    @foreach ($appointments as $key => $app)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>
                                                {{ $app->barber ? $app->barber->name : '' }}
                                            </td>
                                            <td>
                                                {{ $app->customer ? $app->customer->name : '' }}
                                            </td>
                                            <td>
                                                {{ $app->customer ? $app->customer->contact : '' }}
                                            </td>
                                            <td>{{ $app->address }}</td>
                                            <td>{{ $app->appType }}</td>
                                            <td>{{ $app->service->title }}</td>
                                            <td>{{ $app->service_type }}</td>
                                            <td>{{ $app->date }}</td>
                                            <td>{{ $app->slot->from_time }} To {{ $app->slot->to_time }}</td>
                                            <td>{{ $app->status }}</td>
                                            <td>
                                                @if ($app->status == 'Canceled')
                                                    @if ($app->reason != null)
                                                        {{ $app->reason->reason }}
                                                    @endif
                                                @endif

                                                @if ($app->status == 'Review')
                                                    @for ($i = 0; $i < $app->rating->rating; $i++)
                                                        <i class="fa fa-star" style="color:gold;"></i>
                                                    @endfor
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    @if ($app->status == 'Paid')
                                                        <a href="{{ route('completedStatus', $app->id) }}"
                                                            class="btn btn-xs btn-outline btn-success"><i
                                                                class="fa fa-check"></i> Complete</a>
                                                    @elseif($app->status == 'Canceled')
                                                        <i class="fa fa-times" style="color:red;"></i>
                                                    @else
                                                        <i class="fa fa-check" style="color:green;"></i>
                                                    @endif
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
