@extends('layouts.adminapp')

@section('Main-content')
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Push Notifications History</h5>
                    <div class="ibox-tools">
                        <button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#createNotifyModal">
                            Compose New
                        </button>
                    </div>
                </div>
                <div class="ibox-content">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Target</th>
                                <th>Title</th>
                                <th>Message</th>
                                <th>Status</th>
                                <th>Sent At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($notifications as $notification)
                            <tr>
                                <td><span class="label label-info">{{ ucfirst($notification->target) }}</span></td>
                                <td>{{ $notification->title }}</td>
                                <td>{{ Str::limit($notification->message, 50) }}</td>
                                <td>
                                    @if($notification->sent_at)
                                        <span class="label label-primary">Sent</span>
                                    @else
                                        <span class="label label-warning">Draft</span>
                                    @endif
                                </td>
                                <td>{{ $notification->sent_at ? $notification->sent_at->diffForHumans() : 'N/A' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Compose Notification Modal -->
<div class="modal inmodal" id="createNotifyModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated slideInDown">
            <div class="modal-header">
                <i class="fa fa-bell modal-icon" style="color: #764ba2;"></i>
                <h4 class="modal-title">Compose Push Notification</h4>
            </div>
            <form action="{{ route('adminnotifications.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Target Audience</label>
                        <select name="target" class="form-control">
                            <option value="all">All Users</option>
                            <option value="customers">Customers Only</option>
                            <option value="barbers">Barbers Only</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Notification Title</label>
                        <input type="text" name="title" class="form-control" required placeholder="e.g. Special Weekend Discount!">
                    </div>
                    <div class="form-group">
                        <label>Message Body</label>
                        <textarea name="message" class="form-control" required rows="4" placeholder="Enter the message content..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create Draft</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
