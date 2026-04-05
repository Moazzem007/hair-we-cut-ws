@extends('layouts.adminapp')

@section('Main-content')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Payout Requests</h1>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Partner Payout Requests</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Barber / Partner</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Requested On</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($requests as $request)
                                <tr>
                                    <td>{{ $request->id }}</td>
                                    <td>{{ optional($request->barber)->name }}</td>
                                    <td>£{{ number_format($request->amount, 2) }}</td>
                                    <td>
                                        @if($request->status == 'pending')
                                            <span class="badge badge-warning">Pending</span>
                                        @elseif($request->status == 'approved')
                                            <span class="badge badge-success">Approved</span>
                                        @else
                                            <span class="badge badge-danger">Rejected</span>
                                        @endif
                                    </td>
                                    <td>{{ $request->created_at->format('d M Y, h:i A') }}</td>
                                    <td>
                                        @if($request->status == 'pending')
                                            <a href="{{ route('admin.approve_payout', $request->id) }}" class="btn btn-success btn-sm" onclick="return confirm('Are you sure you want to approve this payout? This will deduct the amount from their balance.')">Approve</a>
                                            <a href="{{ route('admin.reject_payout', $request->id) }}" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to reject this request?')">Reject</a>
                                        @else
                                            <button class="btn btn-secondary btn-sm" disabled>Processed</button>
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
</section>
@endsection
