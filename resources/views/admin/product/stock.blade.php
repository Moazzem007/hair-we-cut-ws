@extends('layouts.adminapp')

{{-- @section('title', 'Requests')@endsection --}}


{{-- Main Content --}}

@section('Main-content')


    <div class="row">
        <div class="col-lg-12">
            <br>
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Stock List </h5>
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
                                    <th>Category Name</th>
                                    <th>Product Name</th>
                                    <th>Current Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($stocks as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <!-- <td>{{ $item->category->category_name }}</td> -->
                                        <td>
                                        {{$item->category ? $item->category->category_name : '' }}
                                        </td>
                                        <td>{{ $item->product_name }}</td>
                                        <td>
                                            @if ($item->barberproduct->isNotEmpty())
                                                {{ $item->barberproduct[0]->stock }}
                                            @else
                                                0
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

@endsection

@section('script_code')

@endsection
