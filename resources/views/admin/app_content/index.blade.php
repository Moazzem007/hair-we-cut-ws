@extends('layouts.adminapp')

@section('Main-content')
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>App Content Management (Banners & Promotions)</h5>
                    <div class="ibox-tools">
                        <button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#createContentModal">
                            Add New Content
                        </button>
                    </div>
                </div>
                <div class="ibox-content">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Title</th>
                                <th>Image</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($contents as $content)
                            <tr>
                                <td><span class="label label-{{ $content->type == 'banner' ? 'primary' : 'info' }}">{{ ucfirst($content->type) }}</span></td>
                                <td>{{ $content->title }}</td>
                                <td>
                                    @if($content->image_url)
                                        <img src="{{ asset($content->image_url) }}" width="80" style="border-radius: 4px;">
                                    @else
                                        <span class="text-muted">No Image</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="label label-{{ $content->status ? 'success' : 'default' }}">
                                        {{ $content->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <form action="{{ route('appcontent.destroy', $content->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-white btn-sm" type="submit"><i class="fa fa-trash"></i> </button>
                                    </form>
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

<!-- Simple Create Modal (Simplified for demo) -->
<div class="modal inmodal" id="createContentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <h4 class="modal-title">Add New Content</h4>
            </div>
            <form action="{{ route('appcontent.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Type</label>
                        <select name="type" class="form-control">
                            <option value="banner">Banner</option>
                            <option value="promotion">Promotion</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Image</label>
                        <input type="file" name="image" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Link URL</label>
                        <input type="text" name="link_url" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
