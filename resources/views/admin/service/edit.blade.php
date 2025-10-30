@extends('layouts.adminapp')

{{-- @section('title','Requests')@endsection --}}


{{-- Main Content --}}

@section('Main-content')


<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
         <h2>Add New Product</h2>
         <ol class="breadcrumb">
             <li>
                 <a href="index.html">Dashboard</a>
             </li>
             <li>
                 <a>Products</a>
             </li>
             <li class="active">
                 <strong>Add Product</strong>
             </li>
         </ol>
     </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1">
            <div class="ibox float-e-margins">
                {{--  --}}
                <div class="ibox-title">
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-wrench"></i>
                        </a>
                        <a class="close-link">
                            <i class="fa fa-times"></i>
                        </a>
                    </div>  
                    <div class="ibox-content">

                        <form action="{{route('servicesUpdate')}}" method="POST" role="form" id="form" >
                            {{ method_field('PATCH') }}
                            {{csrf_field()}}

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Title</label>
                                        <input type="text" name="title" class="form-control" value="{{$service->title}}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Price</label>
                                        <input type="number" name="price" class="form-control" value="{{$service->price}}">
                                    </div>
                                </div>

                                <div class="col-md-8 col-md-offset-2">
                                    <div class="form-group">
                                        <label for="">Description</label>
                                        <textarea name="description" id="" cols="30" rows="4" class="form-control">{{$service->description}}</textarea>
                                        
                                    </div>
                                </div>                           
                            </div>  
                                                               
                            <div class="row">
                                
                                <div class="col-md-4 col-md-offset-4">
                                <div class="form-group">
                                    <label for=""></label>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        {{ __('Update') }}
                                    </button>
                                </div>
                                </div>

                            </div>            
                        </form>


                    </div>
                </div>
                {{--  --}}
            </div>
        </div>
    </div>
</div>

@endsection

