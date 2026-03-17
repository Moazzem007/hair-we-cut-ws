@extends('layouts.adminapp')

@section('Main-content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Transaction Ledger (Wallet)</h2>
            <ol class="breadcrumb">
                <li><a href="{{ route('adminDashboard') }}">Home</a></li>
                <li class="active"><strong>Transactions</strong></li>
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
                                        <th>Date</th>
                                        <th>Partner</th>
                                        <th>Customer</th>
                                        <th>Description</th>
                                        <th class="text-right">Debit</th>
                                        <th class="text-right">Credit</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($wallets as $wallet)
                                        <tr>
                                            <td>
                                                <span class="text-muted"><i class="fa fa-calendar"></i> {{ $wallet->created_at->format('M d, Y') }}</span><br>
                                                <small>{{ $wallet->created_at->format('h:i A') }}</small>
                                            </td>
                                            <td><strong>{{ $wallet->barber ? $wallet->barber->name : 'N/A' }}</strong></td>
                                            <td>{{ $wallet->customer ? $wallet->customer->name : 'N/A' }}</td>
                                            <td><small>{{ $wallet->description }}</small></td>
                                            <td class="text-right text-danger font-bold">
                                                @if($wallet->debit > 0)
                                                    -£{{ number_format($wallet->debit, 2) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-right text-navy font-bold">
                                                @if($wallet->credit > 0)
                                                    +£{{ number_format($wallet->credit, 2) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="label label-primary">Settled</span>
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
