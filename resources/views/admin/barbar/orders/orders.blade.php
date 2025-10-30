@extends('admin.barbar.layout')



@section('mainContent')
    {{-- Page Header --}}
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Order List</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ route('adminDashboard') }}">Home</a>
                </li>
                <li>
                    <a>Order</a>
                </li>
                <li class="active">
                    <strong>Order List</strong>
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
                                    <th>Customer Name</th>
                                    <th>Order Type</th>
                                    <th>Contact</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </thead>
                                <tbody>
                                    @foreach ($orders as $key => $order)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $order->customer->name }}</td>
                                            <td>{{ $order->type }}</td>
                                            <td>{{ $order->customer->contact }}</td>
                                            <td>{{ $order->created_at->format('d-m-Y') }}</td>
                                            <td>
                                                @if ($order->status == 'Delivered')
                                                    <span class="label" style='background:#50af53;color:#fff;'><i
                                                            class="fa fa-check"></i>Delivered</span>
                                                @elseif($order->status == 'Pending')
                                                    <span class="label label-success">Pending</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">

                                                    <a href="{{ route('orderInvoiceViewToBarber', $order->id) }}"
                                                        class="btn btn-xs btn-outline btn-success"><i
                                                            class="fa fa-print"></i></a>


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
