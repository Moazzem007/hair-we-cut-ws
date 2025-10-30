@extends('layouts.admin')

{{-- @section('title','Requests')@endsection --}}


{{-- Main Content --}}

@section('main_content')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
         <h2>Edit Product</h2>
         <ol class="breadcrumb">
             <li>
                 <a href="index.html">Dashboard</a>
             </li>
             <li>
                 <a>Products</a>
             </li>
             <li class="active">
                 <strong>Edit Product</strong>
             </li>
         </ol>
     </div>
     <div class="col-lg-2">

     </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1">
            <div class="ibox float-e-margins">
                {{--  --}}
                <div class="ibox-title">

                    <div class="ibox-content">
                        <form action="{{route('product.update',$product->id)}}" method="POST" enctype="multipart/form-data" role="form" id="form">
                            {{method_field('PATCH')}}
                            {{csrf_field()}}
                            <div class="row">

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Select Category</label>
                                        <select name="category" id="" class="form-control" autofocus>
                                            <option value="{{$product->cat_id}}">{{$product->category_name}}</option>
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
                                            <option value="PKR" selected>PKR</option>
                                            <option value="USD">USD</option>
                                            <option value="Aed">AED</option>
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
                                            <option value="1" selected>New Arrival</option>
                                            <option value="2">Best Sellers</option>
                                            <option value="3">Featured</option>
                                            <option value="4">Special Offer</option>
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
                                    <input id="product" type="text" class="form-control" name="product"  value="{{$product->product_name}}" >
                                        @error('product')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="price" class=" col-form-label text-md-right">{{ __('Price') }}</label>
                                        <input id="price" type="text" class="form-control price" name="price" value="{{$product->price}}" >
                                        @error('price')
                                            <span class="invalid-feedback" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="quantity" class="col-form-label text-md-right">{{ __('Quantity') }}</label>
                                        <input id="quantity" type="text" class="form-control" name="quantity" value="{{$product->quantity}}" >
                                        @error('quantity')
                                            <span class="invalid-feedback" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="image" class=" col-form-label text-md-right">{{ __('Image ') }}</label>
                                        <input id="image" type="file" class="form-control" name="image" >
                                        @error('image')
                                            <span class="invalid-feedback" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
    
                                <div class="col-md-4 hidediv" style="display:none;">
                                    <div class="form-group">
                                        <label for="dprice" class=" col-form-label text-md-right">{{ __('Actual Price') }}</label>
                                        <input id="dprice" type="text" class="form-control dprice" value="0" name="dprice" required placeholder="00.00" >
                                        @error('dprice')
                                            <span class="invalid-feedback" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
    
                                <div class="col-md-4 hidediv" style="display:none;">
                                    <div class="form-group">
                                        <label for="percent" class=" col-form-label text-md-right">{{ __('In Percent (%)') }}</label>
                                        <input id="percent" type="text" class="form-control percent" name="percent" readonly>
                                        @error('percent')
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
                                        <textarea name="slug" class="form-control" style="height: 120px;">{{$product->slug}}</textarea>
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
