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

                        <form action="{{route('productstore')}}" method="POST" role="form" id="form"  enctype="multipart/form-data">
                            {{csrf_field()}}

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="category">Select Category</label>
                                        <select name="category" id="category" class="form-control">
                                            <option value="" >--Select Category--</option>
                                            @foreach ( $categories as $category)
                                                <option value="{{$category->id}}">{{$category->category_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('category')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Currency</label>
                                        <select name="currency" id="" class="form-control">
                                            <option >--Select currency--</option>
                                            <option value="Pound" selected>Pound</option>
                                        </select>
                                    </div>
                                    @error('currency')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Product Type</label>
                                        <select name="type" id="type" class="form-control">
                                            <option >--Select Type--</option>
                                            <option value="1" selected>No Discount</option>
                                            <option value="4">Discount</option>
                                        </select>
                                    </div>
                                    @error('type')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                    <label for="product" class="col-form-label text-md-right">{{ __('Product Name') }}</label>
                                    <input id="product" type="text" class="form-control" name="product"  required >
                                        @error('product')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                
                                
                                {{-- <div class="col-md-8">
                                    <div class="form-group">
                                    <label for="slug" class="col-form-label text-md-right">{{ __('Slug') }}</label>
                                    <input id="slug" type="text" class="form-control" name="slug"  required >
                                       
                                    </div>
                                </div> --}}

                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="price" class=" col-form-label text-md-right">{{ __(' Purchase Price') }}</label>
                                        <input id="price" type="text" class="form-control price" name="price" required placeholder="00.00" >
                                        @error('price')
                                            <span class="invalid-feedback" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>


                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="saleprice" class=" col-form-label text-md-right">{{ __('Sale Price') }}</label>
                                        <input id="saleprice" type="text" class="form-control" name="saleprice" required placeholder="00.00" >
                                        @error('saleprice')
                                            <span class="invalid-feedback" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="image" class=" col-form-label text-md-right">{{ __('Image') }} <span>(300 X 300)</span></label>
                                        <input id="image" type="file" class="form-control" name="image"  required> 
                                       
                                        @error('image')
                                            <span class="invalid-feedback" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                

                                <div class="col-md-4 hidediv" style="display:none;">
                                    <div class="form-group">
                                        <label for="percent" class=" col-form-label text-md-right">{{ __('In Percent (%)') }}</label>
                                        <input id="percent" type="text" class="form-control percent" name="percent" value="0">
                                        @error('percent')
                                            <span class="invalid-feedback" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4 hidediv" style="display:none;">
                                    <div class="form-group">
                                        <label for="dprice" class=" col-form-label text-md-right">{{ __('Discount Price') }}</label>
                                        <input id="dprice" type="text" class="form-control dprice" value="0" name="dprice" required placeholder="00.00" >
                                        @error('dprice')
                                            <span class="invalid-feedback" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4 brand" style="display:none;">
                                    <div class="form-group">
                                        <label for="brand" class=" col-form-label text-md-right">{{ __('Brand Name') }}</label>
                                        <input id="brand" type="text" class="form-control" name="brand">
                                        @error('dprice')
                                            <span class="invalid-feedback" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>                                
                            </div>  
                            <div class="row">
                                <div class="col-md-10 col-md-offset-1">

                                    <div class="form-group">
                                        <textarea name="slug" class="form-control" placeholder="Some Description About Product" style="height: 120px;"></textarea>
                                            @error('slug')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                      </div>
                                    <div class="form-group">
                                    {{-- <label for="Description" class="col-form-label text-md-right">{{ __('Description') }}</label> --}}
                                    </div>
                                </div>
                            </div>   

                                    
                            <div class="row">
                                
                                <div class="col-md-4 col-md-offset-4">
                                <div class="form-group">
                                    <label for=""></label>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        {{ __('Add Product') }}
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

@section('script_code')

<script async src="{{asset('admin/js/product.js')}}"></script>
@endsection