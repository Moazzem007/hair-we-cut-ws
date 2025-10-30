@extends('layouts.adminapp')




@section('Main-content')
    {{-- Page Header --}}
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Services List</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ route('adminDashboard') }}">Home</a>
                </li>
                <li>
                    <a>Services</a>
                </li>
                <li class="active">
                    <strong>Services List</strong>
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
                        {{-- <a data-toggle="modal" class="btn btn-primary" href="#modal-form">Add Service</a> --}}
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
                                        <th>Title</th>
                                        <th>Price</th>
                                        <th>Time</th>
                                        <th>Description</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($services as $key => $service)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $service->title }}</td>
                                            <td>{{ $service->price }}</td>
                                            <td>{{ $service->minut }}</td>
                                            <td>{{ $service->description }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    {{-- <a href="{{route('services.edit',$service->id)}}" class="btn btn-xs btn-outline btn-warning"><i class="fa fa-edit"></i> Edit</a> --}}
                                                    <a href="{{ route('servicesDelete', $service->id) }}"
                                                        class="btn btn-xs btn-outline btn-danger"><i
                                                            class="fa fa-trash"></i> Delete</a>
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



        {{-- Modal Form --}}
        <div id="modal-form" class="modal fade" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-10 col-md-offset-1">
                                <h3 class="m-t-none m-b">Add Service</h3>
                                <form action="{{ route('services.store') }}" method="post">
                                    {{ csrf_field() }}
                                    <div class="row">

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Service Type</label>
                                                <select name="type" id="" class="form-control">
                                                    <option value="Hair">Hair Styling</option>
                                                    <option value="Shaving">Shaving</option>
                                                    <option value="Face">Face Masking</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Price (In Pounds)</label>
                                                <input type="number" placeholder="" name="price" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Time (Minuts)</label>
                                                <input type="text" name="minut" class="form-control">
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Title</label>
                                                <input type="text" placeholder="Enter Service Title" name="title"
                                                    class="form-control">
                                            </div>
                                        </div>

                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Description</label>
                                                <textarea name="description" id="" cols="30" rows="4" class="form-control"></textarea>
                                            </div>
                                        </div>

                                        <button class="btn btn-sm btn-primary btn-block"
                                            type="submit"><strong>Save</strong></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Form --}}


    </div>





    {{-- Main Body End --}}
@endsection
