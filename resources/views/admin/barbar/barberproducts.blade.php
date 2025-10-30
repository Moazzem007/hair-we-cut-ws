@extends('admin.barbar.layout')



@section('mainContent')


{{-- Page Header --}}
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Products List</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ route('adminDashboard') }}">Home</a>
            </li>
            <li>
                <a>Products</a>
            </li>
            <li class="active">
                <strong>Products List</strong>
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
                        <table class="table table-striped table-bordered table-hover dataTables-example toggle-arrow-tiny footable" >
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Category Name</th>
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                    <th>Status</th>
                                    <th width="100">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $key => $item)
                                    <tr>
                                        <td>{{$key+1}}</td>
                                        <td>
                                        {{$item->category ? $item->category->category_name : '' }}
                                        </td>
                                        <td>
                                        {{$item->product ? $item->product->product_name : '' }}
                                        </td>
                                        <!-- <td>{{$item->category->category_name}}</td> -->
                                        <!-- <td>{{$item->product->product_name}}</td> -->
                                        <td>{{$item->barber_quantity}}</td>
                                        <td>
                                            @if ($item->status == 1)
                                            <span class="label label-primary">Add</span>
                                            @elseif($item->status == 2)
                                            <span class="label label-success">Approve</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->status == 1)
                                            <div class="btn-group">
                                                <a href="{{route('approve',$item->id)}}" onclick="return confirm('Are you sure')" class="btn btn-primary btn-xs btn-outline"><i class="fa fa-check"></i> Approve </a>
                                            </div>
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





{{-- Main Body End --}}

@endsection
