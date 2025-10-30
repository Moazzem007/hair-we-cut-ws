@extends('layouts.adminapp')

{{-- @section('title', 'Requests')@endsection --}}


{{-- Main Content --}}

@section('Main-content')


    <div class="row">
        <div class="col-lg-12">
            <div class="form-group">
                <br />
                <a href="{{ route('barberproducts.create') }}" class="btn btn-info btn-outline">Assign Product</a>
            </div>
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Assign Product To Barber list </h5>
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
                                <tr>
                                    <th>#</th>
                                    <th>Barber Name</th>
                                    <th>Category Name</th>
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                    <th>Status</th>
                                    <th width="100">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($assignsPro as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $item->barber->name }}</td>
                                        
                                        <!-- <td>{{ $item->category->category_name }}</td> -->
                                        <td>
                                        {{$item->category ? $item->category->category_name : '' }}
                                        </td>
                                        <td>
                                            @if ($item->product != null)
                                                {{ $item->product->product_name }}
                                            @endif
                                        </td>
                                        <td>{{ $item->barber_quantity }}</td>
                                        <td>
                                            @if ($item->status == 1)
                                                <span class="label label-warning">Pending</span>
                                            @elseif($item->status == 2)
                                                <span class="label label-success">Aproved</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('assignproductdelete', $item->id) }}"
                                                    onclick="return confirm('Are you sure to Delete This?')"
                                                    class="btn btn-danger btn-xs btn-outline"><i
                                                        class="fa fa-trash"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach


                            </tbody>
                            <tfoot>
                            </tfoot>
                        </table>

                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection

@section('script_code')

@endsection
