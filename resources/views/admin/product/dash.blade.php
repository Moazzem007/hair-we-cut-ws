@extends('layouts.adminapp')




@section('Main-content')

<style>
    .breadcrumb li a{
        border-radius: 0px !important;
    }
</style>
    

<div class="wrapper wrapper-content">
    <div class="row" style="margin-bottom:20px;">
        <div class="co-md-8">
           
            <ul class="breadcrumb" style="background:transparent;color:#fff;">
                <li><a href="{{route('adminproducts.create')}}" class="btn btn-primary btn-sm">Add Product</a></li>
                <li><a href="{{route('adminproducts.index')}}" class="btn btn-primary btn-sm">Product List</a></li>
                <li><a href="{{route('stocklist')}}" class="btn btn-primary btn-sm">Add Stock</a></li>
                <li><a href="{{route('barberproducts.create')}}" class="btn btn-primary btn-sm">Assign Product To Barber</a></li>
                <li><a href="{{route('barberproducts.index')}}" class="btn btn-primary btn-sm">Barber Product List</a></li>
                <li><a href="{{route('currentstock')}}" class="btn btn-primary btn-sm">Stock</a></li>
            </ul>
        </div>
    </div>

    <div class="row">

        <div class="col-lg-3">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    {{-- <span class="label label-primary pull-right">Today</span> --}}
                    <h5>Total Products</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins">{{number_format($totalProduct)}}</h1>
                    {{-- <div class="stat-percent font-bold text-navy">20% <i class="fa fa-level-up"></i></div> --}}
                    <small></small>
                </div>
            </div>
        </div>

        @foreach ($cates as $category)
            
        
        <div class="col-lg-3">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <span class="label label-info pull-right">Product Category</span>
                    <h5>{{$category->category_name}}</h5>
                </div>
                <div class="ibox-content">
                    @php
                        $stock=0;
                        $assign=0;
                    @endphp
                    @foreach ($category->proudct as $item)
                        @php $stock = $item->total @endphp
                    @endforeach

                    @foreach ($category->barberproduct as $item2)
                        @php $assign = $item2->assigntotal @endphp
                    @endforeach
                    
                    <h1 class="no-margins">{{number_format($stock - $assign)}}</h1>

                    {{-- <div class="stat-percent font-bold text-info">40% <i class="fa fa-level-up"></i></div> --}}
                    <small></small>
                </div>
            </div>
        </div>

        @endforeach
    </div>



</div>

@endsection