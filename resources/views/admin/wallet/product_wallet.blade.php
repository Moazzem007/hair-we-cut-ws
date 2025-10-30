@extends('layouts.adminapp')




@section('Main-content')
    {{-- Page Header --}}
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2> Product Wallet List</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ route('adminDashboard') }}">Home</a>
                </li>
                <li>
                    <a> Product Wallet</a>
                </li>
                <li class="active">
                    <strong> Product Wallet List</strong>
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
                                        <th>Barber Name</th>
                                        <th>Customer Name</th>
                                        <th>Description</th>
                                        <th>Debit</th>
                                        <th>Credit</th>
                                        <th>Commision</th>
                                        <th>Date & Time</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($wallets as $key => $wallet)
                                        <tr>
                                            <th>{{ $key + 1 }}</th>
                                            <td>{{ $wallet->barber ? $wallet->barber->name : '' }}</td>
                                            <td>{{ $wallet->customer ? $wallet->customer->name : '' }}</td>
                                            <td>{{ $wallet->description }}</td>
                                            <td>{{ $wallet->debit }}</td>
                                            <td>{{ $wallet->credit }}</td>
                                            <td>{{ $wallet->com_amount }}</td>
                                            <td>{{ $wallet->created_at->format('d-m-Y') }}</td>
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
