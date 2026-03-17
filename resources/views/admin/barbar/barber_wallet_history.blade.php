@extends('layouts.adminapp')



@section('Main-content')
    {{-- Page Header --}}
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Payment History</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ route('adminDashboard') }}">Home</a>
                </li>
                <li>
                    <a>Payment</a>
                </li>
                <li class="active">
                    <strong>Payment History</strong>
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
                                    <th>Inv #</th>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Balance</th>
                                </thead>
                                <tbody>
                                    @php
                                        $bal2 = 0;
                                    @endphp
                                    @if($barber && $barber->wallet && $barber->wallet->count() > 0)
                                        @foreach($barber->wallet as $index => $pay)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>#{{ $pay->inv }}</td>
                                            <td>{{ $pay->created_at->format('M d, Y') }}</td>
                                            <td>{{ $pay->description ?? 'Transaction' }}</td>
                                            <td class="text-danger">{{ $pay->debit > 0 ? '-$'.$pay->debit : '-' }}</td>
                                            <td class="text-success">{{ $pay->credit > 0 ? '+$'.$pay->credit : '-' }}</td>
                                            <td>
                                                @php
                                                    $bal = $pay->credit - $pay->debit;
                                                    $bal2 += $bal;
                                                @endphp
                                                <strong>${{ number_format($bal2, 2) }}</strong>
                                            </td>
                                        </tr>
                                        @endforeach
                                        @else
                                            <tr>
                                                <td colspan="7" class="text-center">No wallet records found or invalid partner reference.</td>
                                            </tr>
                                        @endif
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
