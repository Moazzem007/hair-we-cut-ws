@extends('admin.barbar.layout')



@section('mainContent')






    {{-- Page Header --}}
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Sell All Products List</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ route('adminDashboard') }}">Home</a>
                </li>
                <li>
                    <a>Market place</a>
                </li>
                <li class="active">
                    <strong>Market Sell Products All</strong>
                </li>
            </ol>
        </div>
    </div>

    {{-- Page Header  End --}}


    {{-- Main Body --}}

    <div class="wrapper wrapper-content animated fadeInRight">
    <!-- Button trigger modal -->
<a href="{{route('marketproducts')}}" type="button" class="btn btn-primary" d>
  My Products
</a>
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
                                    <th>Image</th>
                                    <th>Creater</th>
                                    <th>Product Name</th>
                                    <!-- <th>Category</th> -->
                                    <th>Brand</th>
                                    <th>Price</th>
                                    <th>Dis_Price</th>
                                    <th>Shift_Cost</th>
                                    <!-- <th>Short Desc</th> -->
                                    <th>Description</th>
                                    <!-- <th>Video</th> -->
                                    <th>Specification</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                 

                                </thead>
                                <tbody>
                                @foreach ($show as $key => $view)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>
                                            <img src="{{asset($view->image)}}" alt="" style="width:80px; height:80px;">    
                                            </td>
                                            <td>{{ $view->job_creater }}</td>
                                            <td>{{ $view->product_name }}</td>
                                            <!-- <td>{{ $view->category }}</td> -->
                                            <td>{{ $view->brand }}</td>
                                            <td>{{ $view->price }}</td>
                                            <td>{{ $view->discountprice }}</td>
                                            <td>{{ $view->shift_cost }}</td>
                                            <!-- <td>{{ $view->short_description }}</td> -->
                                            <td>{{ $view->detail_description }}</td>
                                            <!-- <td>
                                            <video src="{{asset($view->video)}}" alt="" style="width:80px; height:80px;">    
                                            </td> -->
                                            <td>{{ $view->specification }}</td>
                                            <td>{{ $view->status }}</td>
                                            <td> <div class="btn-group">
        
                                                <a href="{{route('marketproduct_view',$view->id)}}"
                                                    class="btn btn-xs btn-outline btn-success"><i
                                                        class="fa fa-print"></i></a>
    
    
                                            </div></td>
                                        
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


@section('script')
<script>
  


 @if(Session::has('status'))
    toastr.success("{{ Session::get('status') }}")
@endif
</script>


@endsection







