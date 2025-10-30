@extends('layouts.adminapp')

{{-- @section('title','Requests')@endsection --}}


{{-- Main Content --}}

@section('Main-content')


<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            <br /> 
            <a href="{{route('adminproducts.create')}}" class="btn btn-info btn-outline">Add Product</a>
        </div>
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Products list </h5>
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
                                <th>Image</th>
                                <th>Category</th>
                                <th data-toggle="true">product Name</th>
                                <th data-hide="all">Created_at</th>
                                <th data-hide="all">Slug</th>
                                <th data-hide="all">Percent</th>
                                <th data-hide="all">Discount</th>
                                <th>Price</th>
                                <th>Sale Price</th>
                                <th>Dis. Type</th>
                                <th width="100">Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach( $products as $key => $item)
                                <tr>
                                    <td>{{$key+1}}</td>
                                
                                    <td><img src="{{asset('public/products/'.$item['img'])}}" class="img-responsive" width="50"  alt=""></td>
                                    <td>{{$item['category']['category_name']}}</td>
                                    <td>{{$item['product_name']}}</td>
                                    <td>
                                        {{$item->created_at->format('d-m-Y')}}
                                    </td>
                                    <td>
                                        {{$item->slug}}
                                    </td>
                                    <td>
                                        {{$item->percent}}
                                    </td>
                                    <td>
                                        {{$item->dprice}}
                                    </td>
                                    <td>{{$item->price}}  <i class="fa fa-gbp"></i></td>
                                    <td>{{$item->sale_price}} <i class="fa fa-gbp"></i></td>

                                    <td>@if ($item->type == 1)
                                        No Discount
                                        @else  
                                        Discount
                                        @endif 
                                    </td>
                                    <td>
                                        {{-- <a href="{{route('adminproducts.edit',$item['id'])}}" class="btn btn-warning btn-xs pull-left btn-outline" style="margin-right:10px;"><i class="glyphicon glyphicon-edit"></i> Edit</a> --}}
                                        <a href="{{route('productdestroy',$item['id'])}}" class="btn btn-danger btn-xs pull-left btn-outline" style="margin-right:10px;"><i class="glyphicon glyphicon-trash"></i> Delete</a>
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
        {{-- <div class="row">
            @foreach( $products as $key => $item)
                
            <div class="col-md-3">
                <div class="ibox">
                    <div class="ibox-content product-box">

                        <div class="product-imitation">
                            <img src="{{asset('/storage/images/product/'.$item['img'])}}" class="img-responsive" alt="">
                        </div>
                        <div class="product-desc">
                            <span class="product-price">
                                {{$item['price']}}
                            </span>
                            <small class="text-muted">{{$item['category']['category_name']}} @if($item['brand'] != '0') / {{$item['brand']}} @endif</small>
                            <a href="#" class="product-name"> {{$item['product_name']}}</a>



                            <div class="small m-t-xs">
                                {{$item['slug']}}
                            </div>
                            <div class="m-t text-righ">
                                <a href="{{route('productedit',$item['id'])}}" class="btn btn-warning btn-xs pull-left btn-outline" style="margin-right:10px;"><i class="glyphicon glyphicon-edit"></i> Edit</a>
                                <a href="{{route('productdestroy',$item['id'])}}" class="btn btn-danger btn-xs pull-left btn-outline" style="margin-right:10px;"><i class="glyphicon glyphicon-trash"></i> Delete</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            
        </div> --}}
    </div>
</div>

@endsection

@section('script_code')
    
@endsection