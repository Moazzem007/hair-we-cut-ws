@extends('layouts.adminapp')

@section('Main-content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Commission & Reconciliation</h2>
            <ol class="breadcrumb">
                <li><a href="{{ route('adminDashboard') }}">Home</a></li>
                <li class="active"><strong>Commission</strong></li>
            </ol>
        </div>
    </div>

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-4">
                <div class="widget-stat stat-revenue">
                    <span class="stat-label">Total Commission Collected</span>
                    <h2 class="no-margins"><i class="fa fa-gbp"></i> {{ number_format(App\Models\Wallet::sum('com_amount'), 2) }}</h2>
                    <small>Overview of all time platform earnings</small>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Platform Commission Settings</h5>
                        <div class="ibox-tools">
                            <a data-toggle="modal" class="btn btn-primary btn-xs" href="#modal-form">Update Percentages</a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date Set</th>
                                        <th>Service Comm (%)</th>
                                        <th>Product Comm (%)</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($coms as $key => $com)
                                        <tr @if($loop->first) style="font-weight: bold; background: #fafafa;" @endif>
                                            <td>{{ $com->created_at->format('M d, Y') }}</td>
                                            <td>{{ $com->percent }}%</td>
                                            <td>{{ $com->product }}%</td>
                                            <td>
                                                @if($loop->first)
                                                    <span class="label label-primary">Current Active</span>
                                                @else
                                                    <span class="label label-default">History</span>
                                                @endif
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

    <!-- Modal remains largely same but styled -->
    <div id="modal-form" class="modal fade" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">New Commission Rates</h4>
                </div>
                <form action="{{ route('commission.store') }}" method="post">
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Service Booking Commission (%)</label>
                            <input type="number" required name="percent" class="form-control" placeholder="e.g. 20">
                            <small class="text-muted">Standard rate for salon appointments.</small>
                        </div>
                        <div class="form-group">
                            <label>Barber Product Commission (%)</label>
                            <input type="number" required name="product" class="form-control" placeholder="e.g. 10">
                            <small class="text-muted">Rate for products sold via the app.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-dismiss="modal">Cancel</button>
                        <button class="btn btn-primary" type="submit">Apply New Rates</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
